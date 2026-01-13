const hre = require("hardhat");
const fs = require("fs");
const path = require("path");

async function main() {
  console.log("Starting deployment...");

  // Get the deployer account
  const [deployer] = await hre.ethers.getSigners();
  console.log("Deploying contracts with account:", deployer.address);

  const balance = await hre.ethers.provider.getBalance(deployer.address);
  console.log("Account balance:", hre.ethers.formatEther(balance), "ETH");

  // Deploy IssuerRegistry
  console.log("\nDeploying IssuerRegistry...");
  const IssuerRegistry = await hre.ethers.getContractFactory("IssuerRegistry");
  const issuerRegistry = await IssuerRegistry.deploy();
  await issuerRegistry.waitForDeployment();
  const issuerRegistryAddress = await issuerRegistry.getAddress();
  console.log("IssuerRegistry deployed to:", issuerRegistryAddress);

  // Deploy CredentialRegistry
  console.log("\nDeploying CredentialRegistry...");
  const CredentialRegistry = await hre.ethers.getContractFactory("CredentialRegistry");
  const credentialRegistry = await CredentialRegistry.deploy(issuerRegistryAddress);
  await credentialRegistry.waitForDeployment();
  const credentialRegistryAddress = await credentialRegistry.getAddress();
  console.log("CredentialRegistry deployed to:", credentialRegistryAddress);

  // Save deployment addresses
  const deploymentInfo = {
    network: hre.network.name,
    chainId: (await hre.ethers.provider.getNetwork()).chainId.toString(),
    deployer: deployer.address,
    deployedAt: new Date().toISOString(),
    contracts: {
      IssuerRegistry: issuerRegistryAddress,
      CredentialRegistry: credentialRegistryAddress
    }
  };

  const deploymentsDir = path.join(__dirname, "../deployments");
  if (!fs.existsSync(deploymentsDir)) {
    fs.mkdirSync(deploymentsDir, { recursive: true });
  }

  const deploymentFile = path.join(
    deploymentsDir,
    `${hre.network.name}-deployment.json`
  );

  fs.writeFileSync(deploymentFile, JSON.stringify(deploymentInfo, null, 2));
  console.log("\nDeployment info saved to:", deploymentFile);

  // Also save to Laravel config
  const laravelConfigDir = path.join(__dirname, "../../config");
  const laravelConfigFile = path.join(laravelConfigDir, "blockchain.php");

  const laravelConfig = `<?php

return [
    'network' => '${hre.network.name}',
    'chain_id' => ${deploymentInfo.chainId},
    'issuer_registry_address' => '${issuerRegistryAddress}',
    'credential_registry_address' => '${credentialRegistryAddress}',
    'rpc_url' => env('BLOCKCHAIN_RPC_URL', 'http://127.0.0.1:8545'),
    'deployer_address' => '${deployer.address}',
];
`;

  fs.writeFileSync(laravelConfigFile, laravelConfig);
  console.log("Laravel config saved to:", laravelConfigFile);

  console.log("\n✅ Deployment complete!");
  console.log("\n📋 Summary:");
  console.log("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
  console.log("Network:", hre.network.name);
  console.log("IssuerRegistry:", issuerRegistryAddress);
  console.log("CredentialRegistry:", credentialRegistryAddress);
  console.log("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");

  // Verify on Etherscan if on a public network
  if (hre.network.name !== "hardhat" && hre.network.name !== "localhost") {
    console.log("\n⏳ Waiting for block confirmations before verification...");
    await issuerRegistry.deploymentTransaction().wait(6);

    console.log("\n🔍 Verifying contracts on Etherscan...");
    try {
      await hre.run("verify:verify", {
        address: issuerRegistryAddress,
        constructorArguments: []
      });
      console.log("✅ IssuerRegistry verified");
    } catch (error) {
      console.log("❌ IssuerRegistry verification failed:", error.message);
    }

    try {
      await hre.run("verify:verify", {
        address: credentialRegistryAddress,
        constructorArguments: [issuerRegistryAddress]
      });
      console.log("✅ CredentialRegistry verified");
    } catch (error) {
      console.log("❌ CredentialRegistry verification failed:", error.message);
    }
  }
}

main()
  .then(() => process.exit(0))
  .catch((error) => {
    console.error(error);
    process.exit(1);
  });
