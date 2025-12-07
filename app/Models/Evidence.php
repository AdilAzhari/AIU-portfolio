<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evidence extends Model
{
    protected $table = 'evidences';

    protected $guarded = ['id'];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(user::class);
    }

    public function markPinning(): void
    {
        $this->update(['status' => 'pinning']);
    }

    public function markPinned(string $cid): void
    {
        $this->update(['status' => 'pinned', 'cid' => $cid]);
    }

    public function markFailed(): void
    {
        $this->update(['status' => 'failed']);
    }
}
