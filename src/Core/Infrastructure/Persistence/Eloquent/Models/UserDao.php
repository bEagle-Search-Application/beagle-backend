<?php

namespace Beagle\Core\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class UserDao extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const ID = 'id';
    public const EMAIL = 'email';
    public const PASSWORD = 'password';
    public const NAME = 'name';
    public const SURNAME = 'surname';
    public const BIO = 'bio';
    public const LOCATION = 'location';
    public const PHONE_PREFIX = 'phone_prefix';
    public const PHONE = 'phone';
    public const PICTURE = 'picture';
    public const SHOW_REVIEWS = 'show_reviews';
    public const RATING = 'rating';
    public const IS_VERIFIED = 'is_verified';

    public $incrementing = false;

    protected $table = 'users';
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'email',
        'password',
        'name',
        'surname',
        'bio',
        'location',
        'phone_prefix',
        'phone',
        'picture',
        'show_reviews',
        'rating',
        'is_verified',
        'auth_token'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
}
