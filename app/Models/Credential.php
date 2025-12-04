<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\LogsActivity;

class Credential extends Model
{
    use LogsActivity;

    protected $guarded = [];

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
        $this->update(['status' => 'issued']);
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
