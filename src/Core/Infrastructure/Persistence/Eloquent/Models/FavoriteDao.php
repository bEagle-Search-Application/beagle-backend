<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

final class FavoriteDao extends Model
{
    protected $table = 'favorites';
    protected $keyType = 'string';
    protected $fillable = [
        'user_id',
        'ad_id',
    ];

    public $incrementing = false;
}
