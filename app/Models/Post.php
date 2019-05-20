<?php
declare(strict_types = 1);

namespace App\Models;

use App\Traits\ModelBuilderScopes;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Post
 * @package App\Models
 */
class Post extends Model
{
    use ModelBuilderScopes;

    protected $fillable = [
        'title',
        'description',
        'slug',
        'allow_comments',
        'allow_somethings',
        'author_id',
    ];

    protected $guarded = ['id'];

    protected $hidden = ['created_at', 'updated_at'];

    public $timestamps = true;
}
