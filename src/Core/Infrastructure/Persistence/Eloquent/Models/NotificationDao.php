<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

final class NotificationDao extends Model
{
    public const ID = 'id';
    public const USER_ID = 'user_id';
    public const TEXT = 'text';
    public const READ = 'read';

    protected $table = 'notifications';
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'user_id',
        'text',
        'read',
    ];

    public $incrementing = false;
}
