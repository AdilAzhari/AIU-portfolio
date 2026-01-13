<?php

declare(strict_types=1);

namespace App\Http\Controllers\Verifier;

use App\Http\Controllers\Controller;
use App\Models\Credential;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        // Statistics for all credentials
        $stats = [
            'total_credentials' => Credential::count(),
            'issued' => Credential::where('status', 'issued')->count(),
            'pending' => Credential::where('status', 'pending')->count(),
            'revoked' => Credential::where('status', 'revoked')->count(),
        ];

        // Recent issued credentials for verification
        $recentCredentials = Credential::where('status', 'issued')
            ->with(['student', 'issuer', 'evidence'])
            ->latest('issued_at')
            ->take(20)
            ->get()
            ->map(function ($credential) {
                return [
                    'id' => $credential->id,
                    'title' => $credential->title,
                    'description' => $credential->description,
                    'student_name' => $credential->student->name,
                    'issuer_name' => $credential->issuer->name,
                    'status' => $credential->status,
                    'issued_at' => $credential->issued_at?->format('M d, Y H:i'),
                    'has_blockchain' => ! empty($credential->anchor_hash),
                    'verification_url' => route('verify.show', $credential->id),
                ];
            });

        return Inertia::render('Verifier/Dashboard', [
            'stats' => $stats,
            'recentCredentials' => $recentCredentials,
        ]);
    }
}
