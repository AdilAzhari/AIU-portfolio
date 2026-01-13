<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreCredentialRequest;
use App\Models\Credential;
use App\Models\Evidence;
use App\Models\User;
use App\Services\BlockchainService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class CredentialController extends Controller
{
    public function __construct(private readonly BlockchainService $blockchainService)
    {
    }

    public function create(): Response
    {
        // Get all students
        $students = User::whereHas('role', function ($query) {
            $query->where('name', 'student');
        })->select('id', 'name', 'email')->get();

        // Get all evidence files (only from students, or all if admin)
        // Show uploaded, pinning, and pinned evidence
        $evidence = Evidence::with('user:id,name')
            ->whereIn('status', ['uploaded', 'pinning', 'pinned'])
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->metadata['title'] ?? $item->filename,
                    'filename' => $item->filename,
                    'student_name' => $item->user->name,
                    'cid' => $item->cid,
                    'status' => $item->status,
                ];
            });

        return Inertia::render('Issuer/Credentials/Create', [
            'students' => $students,
            'evidence' => $evidence,
        ]);
    }

    // Issuer: create credential for a student
    public function store(StoreCredentialRequest $request): RedirectResponse
    {

        $request->validated();

        $credential = Credential::query()->create([
            'student_id' => $request->student_id,
            'issuer_id' => auth()->id(),
            'evidence_id' => $request->evidence_id,
            'title' => strip_tags($request->title),
            'description' => strip_tags($request->description),
            'status' => 'pending', // Start as pending
        ]);

        $credential->log('credential_created', [
            'student_id' => $credential->student_id,
        ]);

        return back()->with('status', 'Credential created and pending approval.');
    }

    // Issuer: mark credential as "issued"
    public function issue(Credential $credential): RedirectResponse
    {
        // RBAC: only issuer of this credential OR admin
        if (auth()->id() !== $credential->issuer_id && ! auth()->user()->isRole('admin')) {
            abort(403, 'Unauthorized');
        }

        // Mark as issued in database
        $credential->markIssued();
        $credential->log('credential_issued');

        // Issue credential on blockchain if enabled
        if (config('blockchain.enabled') && $this->blockchainService->isConfigured()) {
            // TODO: Add ethereum_address field to users table
            // For now, using a placeholder address
            $studentAddress = $credential->student->ethereum_address ?? '0x0000000000000000000000000000000000000000';

            // Generate content hash (SHA-256 of credential data)
            $contentData = json_encode([
                'id' => $credential->id,
                'student_id' => $credential->student_id,
                'title' => $credential->title,
                'description' => $credential->description,
                'issued_at' => now()->toIso8601String(),
            ]);
            $contentHash = hash('sha256', $contentData);

            // IPFS CID (placeholder for now - will be set when IPFS is implemented)
            $ipfsCid = $credential->cid ?? '';

            // Issue on blockchain
            $result = $this->blockchainService->issueCredential(
                studentAddress: $studentAddress,
                contentHash: $contentHash,
                ipfsCid: $ipfsCid,
                credentialType: 'academic',
                expiresAt: 0 // No expiration
            );

            if ($result['success']) {
                // Store blockchain transaction hash
                $credential->update([
                    'anchor_hash' => $result['transactionHash'] ?? null,
                    'anchored_at' => now(),
                ]);

                return back()->with('status', 'Credential issued successfully and anchored to blockchain.');
            }

            // If blockchain fails, log but don't fail the whole operation
            Log::warning('Blockchain anchoring failed for credential '.$credential->id, $result);
        }

        return back()->with('status', 'Credential successfully issued.');
    }

    // Issuer: revoke credential
    public function revoke(Request $request, Credential $credential): RedirectResponse
    {
        if (auth()->id() !== $credential->issuer_id && ! auth()->user()->isRole('admin')) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        $credential->revoke($request->reason);
        $credential->log('credential_revoked', ['reason' => $request->reason]);

        return back()->with('status', 'Credential revoked.');
    }
}
