// SPDX-License-Identifier: MIT
pragma solidity ^0.8.20;

import "@openzeppelin/contracts/access/AccessControl.sol";

/**
 * @title IssuerRegistry
 * @dev Manages authorized credential issuers
 */
contract IssuerRegistry is AccessControl {
    bytes32 public constant ADMIN_ROLE = keccak256("ADMIN_ROLE");
    bytes32 public constant ISSUER_ROLE = keccak256("ISSUER_ROLE");

    struct Issuer {
        address issuerAddress;
        string name;
        string department;
        bool isActive;
        uint256 registeredAt;
    }

    mapping(address => Issuer) public issuers;
    address[] public issuerAddresses;

    event IssuerRegistered(address indexed issuerAddress, string name, string department);
    event IssuerRevoked(address indexed issuerAddress);
    event IssuerReactivated(address indexed issuerAddress);

    constructor() {
        _grantRole(DEFAULT_ADMIN_ROLE, msg.sender);
        _grantRole(ADMIN_ROLE, msg.sender);
    }

    /**
     * @dev Register a new issuer
     */
    function registerIssuer(
        address _issuerAddress,
        string memory _name,
        string memory _department
    ) external onlyRole(ADMIN_ROLE) {
        require(_issuerAddress != address(0), "Invalid address");
        require(!issuers[_issuerAddress].isActive, "Issuer already registered");

        issuers[_issuerAddress] = Issuer({
            issuerAddress: _issuerAddress,
            name: _name,
            department: _department,
            isActive: true,
            registeredAt: block.timestamp
        });

        issuerAddresses.push(_issuerAddress);
        _grantRole(ISSUER_ROLE, _issuerAddress);

        emit IssuerRegistered(_issuerAddress, _name, _department);
    }

    /**
     * @dev Revoke an issuer's access
     */
    function revokeIssuer(address _issuerAddress) external onlyRole(ADMIN_ROLE) {
        require(issuers[_issuerAddress].isActive, "Issuer not active");

        issuers[_issuerAddress].isActive = false;
        _revokeRole(ISSUER_ROLE, _issuerAddress);

        emit IssuerRevoked(_issuerAddress);
    }

    /**
     * @dev Reactivate a revoked issuer
     */
    function reactivateIssuer(address _issuerAddress) external onlyRole(ADMIN_ROLE) {
        require(!issuers[_issuerAddress].isActive, "Issuer already active");
        require(issuers[_issuerAddress].registeredAt > 0, "Issuer not registered");

        issuers[_issuerAddress].isActive = true;
        _grantRole(ISSUER_ROLE, _issuerAddress);

        emit IssuerReactivated(_issuerAddress);
    }

    /**
     * @dev Check if an address is an active issuer
     */
    function isActiveIssuer(address _address) external view returns (bool) {
        return issuers[_address].isActive && hasRole(ISSUER_ROLE, _address);
    }

    /**
     * @dev Get total number of issuers
     */
    function getIssuerCount() external view returns (uint256) {
        return issuerAddresses.length;
    }

    /**
     * @dev Get issuer details
     */
    function getIssuer(address _issuerAddress) external view returns (Issuer memory) {
        return issuers[_issuerAddress];
    }
}
