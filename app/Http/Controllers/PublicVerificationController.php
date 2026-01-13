<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Credential;
use App\Services\BlockchainService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class PublicVerificationController extends Controller
{
    public function show(Credential $credential): Response
    {
        // Log verification attempt
        ActivityLog::create([
            'actor_id' => auth()->id(),
            'action' => 'credential_verified',
            'subject_type' => Credential::class,
            'subject_id' => $credential->id,
            'meta' => [
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ],
        ]);

        // Check visibility
        if ($credential->evidence?->visibility === 'private' &&
            auth()->id() !== $credential->student_id) {
            abort(403, 'This credential is private');
        }

        // Prepare response data
        $data = [
            'credential' => [
                'id' => $credential->id,
                'title' => $credential->title,
                'description' => $credential->description,
                'status' => $credential->status,
                'student_name' => $credential->student->name,
                'issuer_name' => $credential->issuer->name,
                'issued_at' => $credential->created_at->format('F d, Y'),
                'revoked_at' => $credential->revoked_at?->format('F d, Y'),
                'revocation_reason' => $credential->revocation_reason,
            ],
        ];

        // Blockchain verification
        if ($credential->anchor_hash) {
            try {
                $blockchain = app(BlockchainService::class);
                $onChainData = $blockchain->verifyCredential($credential->id);

                $data['blockchain'] = [
                    'verified' => true,
                    'tx_hash' => $credential->anchor_hash,
                    'status' => $onChainData['status'] ?? 'unknown',
                    'explorer_url' => 'https://sepolia.etherscan.io/tx/'.$credential->anchor_hash,
                ];
            } catch (\Exception $e) {
                Log::error('Blockchain verification failed', ['error' => $e->getMessage()]);
                $data['blockchain'] = [
                    'verified' => false,
                    'error' => 'Unable to verify on blockchain',
                ];
            }
        }

        // IPFS verification
        if ($credential->cid) {
            $ipfsGateway = config('ipfs.pinata.gateway');
            $ipfsUrl = $ipfsGateway.$credential->cid;

            $data['ipfs'] = [
                'cid' => $credential->cid,
                'url' => $ipfsUrl,
                'gateway' => 'Pinata',
            ];

            // Verify hash integrity
            if ($credential->evidence) {
                try {
                    $response = Http::timeout(10)->get($ipfsUrl);
                    if ($response->successful()) {
                        $retrievedHash = hash('sha256', $response->body());
                        $data['ipfs']['integrity_check'] = $retrievedHash === $credential->evidence->sha256;
                    }
                } catch (\Exception $e) {
                    Log::error('IPFS retrieval failed', ['error' => $e->getMessage()]);
                }
            }
        }

        return Inertia::render('Verify/Show', $data);
    }
}
