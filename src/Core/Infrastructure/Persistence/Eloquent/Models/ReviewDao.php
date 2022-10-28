<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

final class ReviewDao extends Model
{
    public const ID = 'id';
    public const USER_ID = 'user_id';
    public const AUTHOR_ID = 'author_id';
    public const AD_ID = 'ad_id';
    public const TEXT = 'text';
    public const RATING = 'rating';

    protected $table = 'reviews';
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'user_id',
        'author_id',
        'ad_id',
        'text',
        'rating',
    ];

    public $incrementing = false;
}
