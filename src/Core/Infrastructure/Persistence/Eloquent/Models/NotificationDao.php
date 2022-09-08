<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

final class NotificationDao extends Model
{
    protected $table = 'notifications';
    protected $keyType = 'string';
    protected $fillable = [
        'user_id',
        'text',
        'read',
    ];

    public $incrementing = false;
}
