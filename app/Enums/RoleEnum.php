<?php

namespace App\Enums;

enum RoleEnum: string
{
    case ADMIN = 'admin';
    case ISSUER = 'issuer';
    case STUDENT = 'student';
    case VERIFIER = 'verifier';
}
