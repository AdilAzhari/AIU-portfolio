<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\RoleEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    protected $casts = [
        'name' => RoleEnum::class,
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
