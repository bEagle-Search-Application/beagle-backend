<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

final class FavoriteDao extends Model
{
    public const ID = 'id';
    public const USER_ID = 'user_id';
    public const AD_ID = 'ad_id';

    protected $table = 'favorites';
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'user_id',
        'ad_id',
    ];

    public $incrementing = false;
}
