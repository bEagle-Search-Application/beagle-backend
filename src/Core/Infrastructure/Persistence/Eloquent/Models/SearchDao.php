<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

final class SearchDao extends Model
{
    protected $table = 'searches';
    protected $keyType = 'string';
    protected $fillable = [
        'user_id',
        'text',
    ];

    public $incrementing = false;
}
