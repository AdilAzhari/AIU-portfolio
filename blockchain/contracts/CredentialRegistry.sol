// SPDX-License-Identifier: MIT
pragma solidity ^0.8.20;

import "./IssuerRegistry.sol";

/**
 * @title CredentialRegistry
 * @dev Main contract for managing student credentials on-chain
 */
contract CredentialRegistry {
    IssuerRegistry public issuerRegistry;

    enum CredentialStatus {
        PENDING,
        ISSUED,
        REVOKED,
        EXPIRED
    }

    struct Credential {
        uint256 id;
        address studentAddress;
        address issuerAddress;
        bytes32 contentHash;      // SHA-256 hash of the credential JSON
        string ipfsCid;            // IPFS CID of the evidence
        string credentialType;     // e.g., "diploma", "certificate", "award"
        CredentialStatus status;
        uint256 issuedAt;
        uint256 expiresAt;         // 0 = no expiration
        uint256 revokedAt;
        string revocationReason;
    }

    // Credential ID => Credential
    mapping(uint256 => Credential) public credentials;

    // Student address => array of credential IDs
    mapping(address => uint256[]) public studentCredentials;

    // Issuer address => array of credential IDs
    mapping(address => uint256[]) public issuerCredentials;

    uint256 public credentialCount;

    // Events
    event CredentialIssued(
        uint256 indexed credentialId,
        address indexed studentAddress,
        address indexed issuerAddress,
        bytes32 contentHash,
        string ipfsCid,
        string credentialType
    );

    event CredentialRevoked(
        uint256 indexed credentialId,
        address indexed revokedBy,
        string reason,
        uint256 revokedAt
    );

    event CredentialVerified(
        uint256 indexed credentialId,
        address indexed verifier,
        bool isValid
    );

    modifier onlyActiveIssuer() {
        require(
            issuerRegistry.isActiveIssuer(msg.sender),
            "Not an active issuer"
        );
        _;
    }

    modifier onlyIssuerOrAdmin(uint256 _credentialId) {
        require(
            credentials[_credentialId].issuerAddress == msg.sender ||
            issuerRegistry.hasRole(issuerRegistry.ADMIN_ROLE(), msg.sender),
            "Not authorized"
        );
        _;
    }

    constructor(address _issuerRegistryAddress) {
        require(_issuerRegistryAddress != address(0), "Invalid issuer registry address");
        issuerRegistry = IssuerRegistry(_issuerRegistryAddress);
    }

    /**
     * @dev Issue a new credential
     */
    function issueCredential(
        address _studentAddress,
        bytes32 _contentHash,
        string memory _ipfsCid,
        string memory _credentialType,
        uint256 _expiresAt
    ) external onlyActiveIssuer returns (uint256) {
        require(_studentAddress != address(0), "Invalid student address");
        require(_contentHash != bytes32(0), "Invalid content hash");
        require(bytes(_ipfsCid).length > 0, "Invalid IPFS CID");

        credentialCount++;
        uint256 newCredentialId = credentialCount;

        credentials[newCredentialId] = Credential({
            id: newCredentialId,
            studentAddress: _studentAddress,
            issuerAddress: msg.sender,
            contentHash: _contentHash,
            ipfsCid: _ipfsCid,
            credentialType: _credentialType,
            status: CredentialStatus.ISSUED,
            issuedAt: block.timestamp,
            expiresAt: _expiresAt,
            revokedAt: 0,
            revocationReason: ""
        });

        studentCredentials[_studentAddress].push(newCredentialId);
        issuerCredentials[msg.sender].push(newCredentialId);

        emit CredentialIssued(
            newCredentialId,
            _studentAddress,
            msg.sender,
            _contentHash,
            _ipfsCid,
            _credentialType
        );

        return newCredentialId;
    }

    /**
     * @dev Revoke a credential
     */
    function revokeCredential(
        uint256 _credentialId,
        string memory _reason
    ) external onlyIssuerOrAdmin(_credentialId) {
        Credential storage credential = credentials[_credentialId];

        require(credential.id > 0, "Credential does not exist");
        require(
            credential.status == CredentialStatus.ISSUED,
            "Credential not in issued status"
        );

        credential.status = CredentialStatus.REVOKED;
        credential.revokedAt = block.timestamp;
        credential.revocationReason = _reason;

        emit CredentialRevoked(_credentialId, msg.sender, _reason, block.timestamp);
    }

    /**
     * @dev Verify a credential
     */
    function verifyCredential(uint256 _credentialId)
        external
        returns (bool isValid, CredentialStatus status, string memory message)
    {
        Credential storage credential = credentials[_credentialId];

        if (credential.id == 0) {
            emit CredentialVerified(_credentialId, msg.sender, false);
            return (false, CredentialStatus.PENDING, "Credential not found");
        }

        if (credential.status == CredentialStatus.REVOKED) {
            emit CredentialVerified(_credentialId, msg.sender, false);
            return (false, CredentialStatus.REVOKED, credential.revocationReason);
        }

        if (credential.expiresAt > 0 && block.timestamp > credential.expiresAt) {
            // Auto-expire
            credential.status = CredentialStatus.EXPIRED;
            emit CredentialVerified(_credentialId, msg.sender, false);
            return (false, CredentialStatus.EXPIRED, "Credential expired");
        }

        emit CredentialVerified(_credentialId, msg.sender, true);
        return (true, CredentialStatus.ISSUED, "Credential is valid");
    }

    /**
     * @dev Get credential details
     */
    function getCredential(uint256 _credentialId)
        external
        view
        returns (Credential memory)
    {
        require(credentials[_credentialId].id > 0, "Credential does not exist");
        return credentials[_credentialId];
    }

    /**
     * @dev Get all credentials for a student
     */
    function getStudentCredentials(address _studentAddress)
        external
        view
        returns (uint256[] memory)
    {
        return studentCredentials[_studentAddress];
    }

    /**
     * @dev Get all credentials issued by an issuer
     */
    function getIssuerCredentials(address _issuerAddress)
        external
        view
        returns (uint256[] memory)
    {
        return issuerCredentials[_issuerAddress];
    }

    /**
     * @dev Verify content hash matches on-chain record
     */
    function verifyContentHash(uint256 _credentialId, bytes32 _contentHash)
        external
        view
        returns (bool)
    {
        return credentials[_credentialId].contentHash == _contentHash;
    }

    /**
     * @dev Get credential count
     */
    function getTotalCredentials() external view returns (uint256) {
        return credentialCount;
    }
}
