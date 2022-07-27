<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

final class TagDao extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';
    protected $fillable = [
        'title',
    ];
}
