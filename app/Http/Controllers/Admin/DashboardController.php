<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Credential;
use App\Models\Evidence;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        // Overall statistics
        $stats = [
            'total_users' => User::count(),
            'total_credentials' => Credential::count(),
            'issued_credentials' => Credential::where('status', 'issued')->count(),
            'pending_credentials' => Credential::where('status', 'pending')->count(),
            'revoked_credentials' => Credential::where('status', 'revoked')->count(),
            'total_evidence' => Evidence::count(),
            'blockchain_anchored' => Credential::whereNotNull('anchor_hash')->count(),
        ];

        // Recent activity
        $recentActivity = ActivityLog::with('actor')
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'actor' => $log->actor->name ?? 'System',
                    'action' => $log->action,
                    'time' => $log->created_at->diffForHumans(),
                    'meta' => $log->meta,
                ];
            });

        // Credentials by month (last 6 months)
        $credentialsByMonth = Credential::where('status', 'issued')
            ->where('created_at', '>=', now()->subMonths(6))
            ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('count(*) as count'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Credentials by issuer (top 5)
        $topIssuers = Credential::with('issuer')
            ->select('issuer_id', DB::raw('count(*) as count'))
            ->groupBy('issuer_id')
            ->orderBy('count', 'desc')
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->issuer->name,
                    'count' => $item->count,
                ];
            });

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentActivity' => $recentActivity,
            'credentialsByMonth' => $credentialsByMonth,
            'topIssuers' => $topIssuers,
        ]);
    }
}
