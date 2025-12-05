<?php

namespace App\Http\Controllers;

use App\Models\Credential;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class PublicVerificationController extends Controller
{
    public function show(Credential $credential)
    {
        // Enforce visibility rules
        if ($credential->student->id !== auth()->id() &&
            $credential->visibility === 'private') {
            abort(403, 'This credential is private.');
        }

        $evidence = $credential->evidence;

        // Basic metadata (always available)
        $verification = [
            'exists' => true,
            'status' => $credential->status,
            'matches_sha' => null,
            'pinned' => $evidence?->cid !== null,
            'cid' => $evidence?->cid,
        ];

        // If evidence exists, do integrity verification
        if ($evidence && $evidence->sha256 && $evidence->cid) {
            try {
                // Retrieve file from local IPFS gateway
                // Requires local IPFS running
                $res = Http::get("http://127.0.0.1:8080/ipfs/{$evidence->cid}");

                if ($res->ok()) {
                    // Hash retrieved content
                    $remoteSha = hash('sha256', $res->body());
                    $verification['matches_sha'] = ($remoteSha === $evidence->sha256);
                }
            } catch (\Throwable) {
                $verification['matches_sha'] = false;
            }
        }

        ActivityLog::create([
            'actor_id' => auth()->id(),    // null if public user
            'action' => 'credential_verified',
            'subject_type' => Credential::class,
            'subject_id' => $credential->id,
            'meta' => [
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ],
        ]);

        return Inertia::render('Verify/Show', [
            'credential' => [
                'id' => $credential->id,
                'title' => $credential->title,
                'description' => $credential->description,
                'status' => $credential->status,
                'revocation_reason' => $credential->revocation_reason,
                'student' => $credential->student->only('id', 'name'),
                'issuer' => $credential->issuer->only('id', 'name'),
                'created_at' => $credential->created_at,
            ],
            'verification' => $verification,
        ]);
    }
}
