<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

final class PersonalAccessTokenDao extends Model
{
    protected $table = 'personal_access_tokens';
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'user_id',
        'token',
    ];

    public $incrementing = false;
}
