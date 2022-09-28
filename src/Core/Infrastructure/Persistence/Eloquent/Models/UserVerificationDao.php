<?php

namespace Beagle\Core\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class UserVerificationDao extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'verification_token';
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'email',
        'token',
        'expired_at'
    ];

    public $incrementing = false;
}
