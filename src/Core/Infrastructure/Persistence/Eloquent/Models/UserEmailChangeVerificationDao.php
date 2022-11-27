<?php

namespace Beagle\Core\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

class UserEmailChangeVerificationDao extends Model
{
    public const USER_ID = 'user_id';
    public const OLD_EMAIL = 'old_email';
    public const NEW_EMAIL = 'new_email';
    public const CONFIRMED = 'confirmed';

    protected $table = 'user_email_change_verifications';
    protected $keyType = 'string';
    protected $fillable = [
        'user_id',
        'old_email',
        'new_email',
        'confirmed'
    ];

    public $incrementing = false;
}
