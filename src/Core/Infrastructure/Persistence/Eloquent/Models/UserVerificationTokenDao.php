<?php

namespace Beagle\Core\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class UserVerificationTokenDao extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'user_verification_tokens';
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'user_id',
        'token',
    ];

    public $incrementing = false;
}
