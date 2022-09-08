<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

final class ReviewDao extends Model
{
    protected $table = 'reviews';
    protected $keyType = 'string';
    protected $fillable = [
        'user_id',
        'author_id',
        'ad_id',
        'text',
        'rating',
    ];

    public $incrementing = false;
}
