<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

final class TagDao extends Model
{
    public const ID = 'id';
    public const TITLE = 'title';

    protected $table = 'tags';
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'title',
    ];

    public $incrementing = false;
}
