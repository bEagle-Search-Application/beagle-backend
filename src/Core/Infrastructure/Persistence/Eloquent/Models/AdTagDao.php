<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

final class AdTagDao extends Model
{
    public const ID = 'id';
    public const TAG_ID = 'tag_id';
    public const AD_ID = 'ad_id';

    protected $table = 'ads_tags';
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'tag_id',
        'ad_id',
    ];

    public $incrementing = false;
}
