<?php

declare(strict_types=1);

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Credential;
use App\Models\Evidence;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $user = auth()->user();

        $credentials = Credential::where('student_id', $user->id)
            ->with(['issuer', 'evidence'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($credential) {
                return [
                    'id' => $credential->id,
                    'title' => $credential->title,
                    'description' => $credential->description,
                    'status' => $credential->status,
                    'issuer_name' => $credential->issuer->name,
                    'issued_at' => $credential->anchored_at?->format('M d, Y') ?? $credential->created_at->format('M d, Y'),
                    'cid' => $credential->cid,
                    'anchor_hash' => $credential->anchor_hash,
                    'revoked_at' => $credential->revoked_at?->format('M d, Y'),
                    'revocation_reason' => $credential->revocation_reason,
                    'has_evidence' => $credential->evidence !== null,
                    'verification_url' => route('verify.show', $credential->id),
                ];
            });

        $evidence = Evidence::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'filename' => $item->filename,
                    'size' => $item->size,
                    'uploaded_at' => $item->created_at->format('M d, Y'),
                    'cid' => $item->cid,
                    'status' => $item->status,
                    'sha256' => substr($item->sha256, 0, 16).'...',
                ];
            });

        $stats = [
            'total_credentials' => $credentials->count(),
            'issued' => $credentials->where('status', 'issued')->count(),
            'pending' => $credentials->where('status', 'pending')->count(),
            'revoked' => $credentials->where('status', 'revoked')->count(),
        ];

        return Inertia::render('Student/Dashboard', [
            'credentials' => $credentials,
            'evidence' => $evidence,
            'stats' => $stats,
        ]);
    }
}
