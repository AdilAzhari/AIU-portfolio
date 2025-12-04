<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{
    public function dashboard(): Response
    {
        
        $stats = cache()->remember('admin_stats', 60, function () {
            return [
                'credentials' => Credential::count(),
                'issued' => Credential::where('status', 'issued')->count(),
                'revoked' => Credential::where('status', 'revoked')->count(),
            ];
        });

        return Inertia::render('Admin/Dashboard');
    }

    public function logs(): Response
    {
        return Inertia::render('Admin/Logs', [
            'logs' => ActivityLog::with('actor')
                ->latest()
                ->limit(200)
                ->get(),
        ]);
    }

    public function exportLogs()
    {
        $logs = ActivityLog::latest()->get();

        $csv = "time,actor,action,meta\n";
        foreach ($logs as $log) {
            $actorName = $log->actor->name ?? '';
            $csv .= "\"{$log->created_at}\",\"{$actorName}\",\"{$log->action}\",\"".json_encode($log->meta)."\"\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename=activity_logs.csv');
    }
}
