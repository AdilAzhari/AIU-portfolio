<?php

namespace App\Enums;

enum Credentialstatus: string
{
    case PENDING = 'pending';
    case REVOKED = 'revoked';
    case ISSUED = 'issued';

    public static function values(): array
    {
        return [
            'Pending',
            'Revoked',
            'Issued',
        ];
    }

}
