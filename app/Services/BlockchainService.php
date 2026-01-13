<?php

declare(strict_types=1);

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Web3\Contract;
use Web3\Providers\HttpProvider;
use Web3\Web3;

class BlockchainService
{
    private Web3 $web3;

    private ?Contract $credentialRegistry = null;

    private ?Contract $issuerRegistry = null;

    private string $credentialRegistryAddress;

    private string $issuerRegistryAddress;

    private string $privateKey;

    private string $adminAddress;

    /**
     * Initialize blockchain connection
     */
    public function __construct()
    {
        if (! config('blockchain.enabled')) {
            return;
        }

        try {
            $rpcUrl = config('blockchain.rpc_url');
            $this->web3 = new Web3(new HttpProvider($rpcUrl));

            $this->credentialRegistryAddress = config('blockchain.contracts.credential_registry') ?? '';
            $this->issuerRegistryAddress = config('blockchain.contracts.issuer_registry') ?? '';
            $this->privateKey = config('blockchain.private_key') ?? '';
            $this->adminAddress = config('blockchain.admin_address') ?? '';

            // Load contract ABIs if they exist
            $this->loadContracts();
        } catch (Exception $e) {
            Log::error('Blockchain initialization failed: '.$e->getMessage());
        }
    }

    /**
     * Load smart contract instances
     */
    private function loadContracts(): void
    {
        $abiPath = config('blockchain.abi_path');

        // Load CredentialRegistry ABI
        $credentialAbiFile = $abiPath.'/CredentialRegistry.sol/CredentialRegistry.json';
        if (file_exists($credentialAbiFile)) {
            $credentialAbi = json_decode(file_get_contents($credentialAbiFile), true)['abi'];
            $this->credentialRegistry = new Contract($this->web3->provider, $credentialAbi);
            $this->credentialRegistry->at($this->credentialRegistryAddress);
        }

        // Load IssuerRegistry ABI
        $issuerAbiFile = $abiPath.'/IssuerRegistry.sol/IssuerRegistry.json';
        if (file_exists($issuerAbiFile)) {
            $issuerAbi = json_decode(file_get_contents($issuerAbiFile), true)['abi'];
            $this->issuerRegistry = new Contract($this->web3->provider, $issuerAbi);
            $this->issuerRegistry->at($this->issuerRegistryAddress);
        }
    }

    /**
     * Issue a credential on the blockchain
     *
     * @param  string  $studentAddress  Ethereum address of the student
     * @param  string  $contentHash  SHA-256 hash of credential content
     * @param  string  $ipfsCid  IPFS content identifier
     * @param  string  $credentialType  Type of credential
     * @param  int  $expiresAt  Expiration timestamp (0 for no expiration)
     * @return array{success: bool, transactionHash?: string, blockchainId?: int, error?: string}
     */
    public function issueCredential(
        string $studentAddress,
        string $contentHash,
        string $ipfsCid,
        string $credentialType = 'academic',
        int $expiresAt = 0
    ): array {
        if (! config('blockchain.enabled')) {
            return [
                'success' => false,
                'error' => 'Blockchain integration is disabled',
            ];
        }

        if (! $this->credentialRegistry) {
            return [
                'success' => false,
                'error' => 'Credential registry contract not loaded. Please compile and deploy contracts first.',
            ];
        }

        try {
            // Convert content hash to bytes32 format
            $contentHashBytes32 = '0x'.$contentHash;

            // Call smart contract method
            $transactionHash = null;
            $blockchainId = null;

            $this->credentialRegistry->send('issueCredential', $studentAddress, $contentHashBytes32, $ipfsCid, $credentialType, $expiresAt, [
                'from' => $this->adminAddress,
                'gas' => config('blockchain.gas.limit'),
            ], function ($err, $result) use (&$transactionHash, &$blockchainId) {
                if ($err !== null) {
                    Log::error('Blockchain transaction error: '.$err->getMessage());

                    return;
                }

                $transactionHash = $result;
                // In a real implementation, you would wait for the transaction to be mined
                // and extract the credentialId from the event logs
            });

            Log::info('Credential issued on blockchain', [
                'student_address' => $studentAddress,
                'transaction_hash' => $transactionHash,
            ]);

            return [
                'success' => true,
                'transactionHash' => $transactionHash,
                'blockchainId' => $blockchainId,
            ];
        } catch (Exception $e) {
            Log::error('Failed to issue credential on blockchain: '.$e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Revoke a credential on the blockchain
     *
     * @param  int  $credentialId  Blockchain credential ID
     * @return array{success: bool, transactionHash?: string, error?: string}
     */
    public function revokeCredential(int $credentialId): array
    {
        if (! config('blockchain.enabled')) {
            return [
                'success' => false,
                'error' => 'Blockchain integration is disabled',
            ];
        }

        if (! $this->credentialRegistry) {
            return [
                'success' => false,
                'error' => 'Credential registry contract not loaded',
            ];
        }

        try {
            $transactionHash = null;

            $this->credentialRegistry->send('revokeCredential', $credentialId, [
                'from' => $this->adminAddress,
                'gas' => config('blockchain.gas.limit'),
            ], function ($err, $result) use (&$transactionHash) {
                if ($err !== null) {
                    Log::error('Blockchain transaction error: '.$err->getMessage());

                    return;
                }

                $transactionHash = $result;
            });

            Log::info('Credential revoked on blockchain', [
                'credential_id' => $credentialId,
                'transaction_hash' => $transactionHash,
            ]);

            return [
                'success' => true,
                'transactionHash' => $transactionHash,
            ];
        } catch (Exception $e) {
            Log::error('Failed to revoke credential on blockchain: '.$e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verify a credential on the blockchain
     *
     * @param  int  $credentialId  Blockchain credential ID
     * @return array{success: bool, isValid?: bool, status?: string, data?: array, error?: string}
     */
    public function verifyCredential(int $credentialId): array
    {
        if (! config('blockchain.enabled')) {
            return [
                'success' => false,
                'error' => 'Blockchain integration is disabled',
            ];
        }

        if (! $this->credentialRegistry) {
            return [
                'success' => false,
                'error' => 'Credential registry contract not loaded',
            ];
        }

        try {
            $credentialData = null;

            $this->credentialRegistry->call('verifyCredential', $credentialId, function ($err, $result) use (&$credentialData) {
                if ($err !== null) {
                    Log::error('Blockchain call error: '.$err->getMessage());

                    return;
                }

                $credentialData = $result;
            });

            if (! $credentialData) {
                return [
                    'success' => false,
                    'error' => 'Credential not found on blockchain',
                ];
            }

            // Parse credential data based on smart contract structure
            // Adjust indices based on actual contract return values
            $isValid = $credentialData['isValid'] ?? false;
            $status = $credentialData['status'] ?? 'unknown';

            return [
                'success' => true,
                'isValid' => $isValid,
                'status' => $status,
                'data' => $credentialData,
            ];
        } catch (Exception $e) {
            Log::error('Failed to verify credential on blockchain: '.$e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Register an issuer on the blockchain
     *
     * @param  string  $issuerAddress  Ethereum address of the issuer
     * @param  string  $name  Issuer name
     * @param  string  $department  Issuer department
     * @return array{success: bool, transactionHash?: string, error?: string}
     */
    public function registerIssuer(string $issuerAddress, string $name, string $department): array
    {
        if (! config('blockchain.enabled')) {
            return [
                'success' => false,
                'error' => 'Blockchain integration is disabled',
            ];
        }

        if (! $this->issuerRegistry) {
            return [
                'success' => false,
                'error' => 'Issuer registry contract not loaded',
            ];
        }

        try {
            $transactionHash = null;

            $this->issuerRegistry->send('registerIssuer', $issuerAddress, $name, $department, [
                'from' => $this->adminAddress,
                'gas' => config('blockchain.gas.limit'),
            ], function ($err, $result) use (&$transactionHash) {
                if ($err !== null) {
                    Log::error('Blockchain transaction error: '.$err->getMessage());

                    return;
                }

                $transactionHash = $result;
            });

            Log::info('Issuer registered on blockchain', [
                'issuer_address' => $issuerAddress,
                'transaction_hash' => $transactionHash,
            ]);

            return [
                'success' => true,
                'transactionHash' => $transactionHash,
            ];
        } catch (Exception $e) {
            Log::error('Failed to register issuer on blockchain: '.$e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check if blockchain is properly configured
     */
    public function isConfigured(): bool
    {
        return config('blockchain.enabled')
            && ! empty($this->credentialRegistryAddress)
            && ! empty($this->issuerRegistryAddress)
            && $this->credentialRegistry !== null
            && $this->issuerRegistry !== null;
    }

    /**
     * Get blockchain connection status
     */
    public function getStatus(): array
    {
        return [
            'enabled' => config('blockchain.enabled'),
            'network' => config('blockchain.network'),
            'rpc_url' => config('blockchain.rpc_url'),
            'credential_registry' => $this->credentialRegistryAddress,
            'issuer_registry' => $this->issuerRegistryAddress,
            'configured' => $this->isConfigured(),
        ];
    }
}