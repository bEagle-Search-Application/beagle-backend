<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

final class TagDao extends Model
{
    protected $table = 'tags';
    protected $keyType = 'string';
    protected $fillable = [
        'title',
    ];

    public $incrementing = false;
}
