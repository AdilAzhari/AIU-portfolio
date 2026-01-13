<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Credential extends Model
{
    use LogsActivity;

    protected $fillable = [
        'student_id',
        'issuer_id',
        'evidence_id',
        'title',
        'description',
        'status',
        'revocation_reason',
        'revoked_at',
        'issued_at',
        'cid',
        'anchor_hash',
        'anchored_at',
    ];

    protected $casts = [
        'revocation_reason' => 'string',
        'revoked_at' => 'datetime',
        'issued_at' => 'datetime',
        'anchor_hash' => 'string',
        'anchored_at' => 'datetime',
        'cid' => 'integer',
        'status' => 'string',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issuer_id');
    }

    public function evidence(): BelongsTo
    {
        return $this->belongsTo(Evidence::class);
    }

    public function markIssued(): void
    {
        $this->update([
            'status' => 'issued',
            'issued_at' => now(),
        ]);
    }

    public function revoke(string $reason): void
    {
        $this->update([
            'status' => 'revoked',
            'revocation_reason' => $reason,
            'revoked_at' => now(),
        ]);
    }
}
