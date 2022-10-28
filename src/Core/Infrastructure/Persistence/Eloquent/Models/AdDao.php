<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

final class AdDao extends Model
{
    public const ID = 'id';
    public const USER_ID = 'user_id';
    public const TITLE = 'title';
    public const STATUS = 'status';
    public const TYPE = 'type';
    public const LAST_LOCATION = 'last_location';
    public const DESCRIPTION = 'description';
    public const REWARD = 'reward';

    protected $table = 'ads';
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'user_id',
        'title',
        'status',
        'type',
        'last_location',
        'description',
        'reward',
    ];

    public $incrementing = false;
}
