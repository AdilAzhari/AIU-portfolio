const { expect } = require("chai");
const { ethers } = require("hardhat");
const { time } = require("@nomicfoundation/hardhat-network-helpers");

describe("CredentialRegistry", function () {
  let issuerRegistry;
  let credentialRegistry;
  let owner, issuer, student, verifier;

  beforeEach(async function () {
    [owner, issuer, student, verifier] = await ethers.getSigners();

    // Deploy IssuerRegistry
    const IssuerRegistry = await ethers.getContractFactory("IssuerRegistry");
    issuerRegistry = await IssuerRegistry.deploy();
    await issuerRegistry.waitForDeployment();

    // Deploy CredentialRegistry
    const CredentialRegistry = await ethers.getContractFactory("CredentialRegistry");
    credentialRegistry = await CredentialRegistry.deploy(
      await issuerRegistry.getAddress()
    );
    await credentialRegistry.waitForDeployment();

    // Register an issuer
    await issuerRegistry.registerIssuer(
      issuer.address,
      "Computer Science Department",
      "CS"
    );
  });

  describe("Deployment", function () {
    it("Should set the correct issuer registry", async function () {
      expect(await credentialRegistry.issuerRegistry()).to.equal(
        await issuerRegistry.getAddress()
      );
    });

    it("Should start with zero credentials", async function () {
      expect(await credentialRegistry.credentialCount()).to.equal(0);
    });
  });

  describe("Credential Issuance", function () {
    it("Should allow active issuer to issue credential", async function () {
      const contentHash = ethers.keccak256(ethers.toUtf8Bytes("test credential"));
      const ipfsCid = "QmTest123";
      const credentialType = "diploma";

      await expect(
        credentialRegistry
          .connect(issuer)
          .issueCredential(student.address, contentHash, ipfsCid, credentialType, 0)
      )
        .to.emit(credentialRegistry, "CredentialIssued")
        .withArgs(1, student.address, issuer.address, contentHash, ipfsCid, credentialType);

      expect(await credentialRegistry.credentialCount()).to.equal(1);
    });

    it("Should reject issuance from non-issuer", async function () {
      const contentHash = ethers.keccak256(ethers.toUtf8Bytes("test"));
      const ipfsCid = "QmTest";

      await expect(
        credentialRegistry
          .connect(student)
          .issueCredential(student.address, contentHash, ipfsCid, "test", 0)
      ).to.be.revertedWith("Not an active issuer");
    });

    it("Should store credential data correctly", async function () {
      const contentHash = ethers.keccak256(ethers.toUtf8Bytes("test credential"));
      const ipfsCid = "QmTest123";
      const credentialType = "certificate";

      await credentialRegistry
        .connect(issuer)
        .issueCredential(student.address, contentHash, ipfsCid, credentialType, 0);

      const credential = await credentialRegistry.getCredential(1);

      expect(credential.id).to.equal(1);
      expect(credential.studentAddress).to.equal(student.address);
      expect(credential.issuerAddress).to.equal(issuer.address);
      expect(credential.contentHash).to.equal(contentHash);
      expect(credential.ipfsCid).to.equal(ipfsCid);
      expect(credential.credentialType).to.equal(credentialType);
      expect(credential.status).to.equal(1); // ISSUED
    });
  });

  describe("Credential Revocation", function () {
    beforeEach(async function () {
      const contentHash = ethers.keccak256(ethers.toUtf8Bytes("test"));
      await credentialRegistry
        .connect(issuer)
        .issueCredential(student.address, contentHash, "QmTest", "test", 0);
    });

    it("Should allow issuer to revoke their credential", async function () {
      await expect(
        credentialRegistry.connect(issuer).revokeCredential(1, "Revoked for testing")
      )
        .to.emit(credentialRegistry, "CredentialRevoked")
        .withArgs(1, issuer.address, "Revoked for testing", await time.latest());

      const credential = await credentialRegistry.getCredential(1);
      expect(credential.status).to.equal(2); // REVOKED
    });

    it("Should reject revocation from unauthorized user", async function () {
      await expect(
        credentialRegistry.connect(student).revokeCredential(1, "Unauthorized")
      ).to.be.revertedWith("Not authorized");
    });

    it("Should not allow revoking already revoked credential", async function () {
      await credentialRegistry.connect(issuer).revokeCredential(1, "First revocation");

      await expect(
        credentialRegistry.connect(issuer).revokeCredential(1, "Second revocation")
      ).to.be.revertedWith("Credential not in issued status");
    });
  });

  describe("Credential Verification", function () {
    it("Should verify valid credential", async function () {
      const contentHash = ethers.keccak256(ethers.toUtf8Bytes("test"));
      await credentialRegistry
        .connect(issuer)
        .issueCredential(student.address, contentHash, "QmTest", "test", 0);

      const [isValid, status, message] = await credentialRegistry
        .connect(verifier)
        .verifyCredential.staticCall(1);

      expect(isValid).to.be.true;
      expect(status).to.equal(1); // ISSUED
      expect(message).to.equal("Credential is valid");
    });

    it("Should detect revoked credential", async function () {
      const contentHash = ethers.keccak256(ethers.toUtf8Bytes("test"));
      await credentialRegistry
        .connect(issuer)
        .issueCredential(student.address, contentHash, "QmTest", "test", 0);

      await credentialRegistry.connect(issuer).revokeCredential(1, "Test revocation");

      const [isValid, status, message] = await credentialRegistry
        .connect(verifier)
        .verifyCredential.staticCall(1);

      expect(isValid).to.be.false;
      expect(status).to.equal(2); // REVOKED
      expect(message).to.equal("Test revocation");
    });

    it("Should detect non-existent credential", async function () {
      const [isValid, status, message] = await credentialRegistry
        .connect(verifier)
        .verifyCredential.staticCall(999);

      expect(isValid).to.be.false;
      expect(message).to.equal("Credential not found");
    });
  });

  describe("Content Hash Verification", function () {
    it("Should verify matching content hash", async function () {
      const contentHash = ethers.keccak256(ethers.toUtf8Bytes("test credential"));
      await credentialRegistry
        .connect(issuer)
        .issueCredential(student.address, contentHash, "QmTest", "test", 0);

      const matches = await credentialRegistry.verifyContentHash(1, contentHash);
      expect(matches).to.be.true;
    });

    it("Should reject non-matching content hash", async function () {
      const contentHash = ethers.keccak256(ethers.toUtf8Bytes("test credential"));
      await credentialRegistry
        .connect(issuer)
        .issueCredential(student.address, contentHash, "QmTest", "test", 0);

      const wrongHash = ethers.keccak256(ethers.toUtf8Bytes("wrong"));
      const matches = await credentialRegistry.verifyContentHash(1, wrongHash);
      expect(matches).to.be.false;
    });
  });

  describe("Query Functions", function () {
    it("Should get student credentials", async function () {
      const contentHash = ethers.keccak256(ethers.toUtf8Bytes("test"));

      await credentialRegistry
        .connect(issuer)
        .issueCredential(student.address, contentHash, "QmTest1", "test", 0);

      await credentialRegistry
        .connect(issuer)
        .issueCredential(student.address, contentHash, "QmTest2", "test", 0);

      const studentCreds = await credentialRegistry.getStudentCredentials(student.address);
      expect(studentCreds.length).to.equal(2);
      expect(studentCreds[0]).to.equal(1);
      expect(studentCreds[1]).to.equal(2);
    });

    it("Should get issuer credentials", async function () {
      const contentHash = ethers.keccak256(ethers.toUtf8Bytes("test"));

      await credentialRegistry
        .connect(issuer)
        .issueCredential(student.address, contentHash, "QmTest1", "test", 0);

      const issuerCreds = await credentialRegistry.getIssuerCredentials(issuer.address);
      expect(issuerCreds.length).to.equal(1);
      expect(issuerCreds[0]).to.equal(1);
    });
  });
});
