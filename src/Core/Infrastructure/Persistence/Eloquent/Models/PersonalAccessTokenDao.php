<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

final class PersonalAccessTokenDao extends Model
{
    public const ID = 'id';
    public const USER_ID = 'user_id';
    public const TOKEN = 'token';

    protected $table = 'personal_access_tokens';
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'user_id',
        'token',
    ];

    public $incrementing = false;
}
