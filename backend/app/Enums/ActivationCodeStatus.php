<?php

namespace App\Enums;

enum ActivationCodeStatus: string
{
    case UNUSED = 'unused';
    case ACTIVE = 'active';
    case EXPIRED = 'expired';
    case REVOKED = 'revoked';
}
