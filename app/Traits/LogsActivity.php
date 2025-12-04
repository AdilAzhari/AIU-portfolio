<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait LogsActivity
{
    public function log(string $action, array $meta = []): void
    {
        ActivityLog::create([
            'actor_id' => auth()->id(),
            'action' => $action,
            'subject_type' => static::class,
            'subject_id' => $this->id,
            'meta' => $meta,
        ]);
    }
}
