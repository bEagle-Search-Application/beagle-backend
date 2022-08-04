<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

final class AdTagDao extends Model
{
    protected $table = 'ads_tags';
    protected $keyType = 'string';
    protected $fillable = [
        'tag_id',
        'ad_id',
    ];

    public $incrementing = false;
}
