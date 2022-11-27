<?php

namespace Beagle\Core\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

class UserEmailVerificationDao extends Model
{
    public const ID = 'id';
    public const USER_ID = 'user_id';
    public const TOKEN = 'token';

    protected $table = 'user_email_verifications';
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'user_id',
        'token',
    ];

    public $incrementing = false;
}
