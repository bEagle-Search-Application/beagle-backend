<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

final class SearchDao extends Model
{
    public const ID = 'id';
    public const USER_ID = 'user_id';
    public const TEXT = 'text';

    protected $table = 'searches';
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'user_id',
        'text',
    ];

    public $incrementing = false;
}
