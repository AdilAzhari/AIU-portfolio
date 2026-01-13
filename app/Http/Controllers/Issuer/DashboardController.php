<?php

declare(strict_types=1);

namespace App\Http\Controllers\Issuer;

use App\Http\Controllers\Controller;
use App\Models\Credential;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $user = auth()->user();

        // Statistics for this issuer
        $stats = [
            'total_issued' => Credential::where('issuer_id', $user->id)->count(),
            'pending' => Credential::where('issuer_id', $user->id)->where('status', 'pending')->count(),
            'issued' => Credential::where('issuer_id', $user->id)->where('status', 'issued')->count(),
            'revoked' => Credential::where('issuer_id', $user->id)->where('status', 'revoked')->count(),
        ];

        // Recent credentials issued by this issuer
        $recentCredentials = Credential::where('issuer_id', $user->id)
            ->with(['student', 'evidence'])
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($credential) {
                return [
                    'id' => $credential->id,
                    'title' => $credential->title,
                    'student_name' => $credential->student->name,
                    'status' => $credential->status,
                    'created_at' => $credential->created_at->format('M d, Y'),
                    'issued_at' => $credential->issued_at?->format('M d, Y'),
                ];
            });

        // Get all students for credential creation
        $students = User::whereHas('role', function ($query) {
            $query->where('name', 'student');
        })->select('id', 'name', 'email')->get();

        return Inertia::render('Issuer/Dashboard', [
            'stats' => $stats,
            'recentCredentials' => $recentCredentials,
            'students' => $students,
        ]);
    }
}
