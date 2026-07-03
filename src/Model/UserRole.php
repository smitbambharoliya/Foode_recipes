<?php

namespace App\Model;

use Symfony\Component\Translation\TranslatableMessage;

enum UserRole: string 
{
    case User  = 'ROLE_USER';
    case Admin = 'ROLE_ADMIN';

  
    public function label(): TranslatableMessage
    {
        return match ($this) {
            self::User  => new TranslatableMessage('user_role.user'),
            self::Admin => new TranslatableMessage('user_role.admin'),
        };
    }
}
