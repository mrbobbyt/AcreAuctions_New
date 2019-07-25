<?php
declare(strict_types = 1);

namespace App\Models;

use App\Traits\ModelBuilderScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    /**
     * Get related author
     * @return BelongsTo
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }

    /**
     * Get related images
     * @return HasMany
     */
    public function images()
    {
        return $this->hasMany(Image::class, 'post_id', 'id')
            ->where('entity_type', Image::TYPE_LISTING);
    }

    /**
     * Get related images - fullsize and preview
     * @return HasMany
     */
    public function gallery()
    {
        return $this->hasMany(FullsizePreview::class, 'post_id', 'id')
            ->where('desc_image', FullsizePreview::IMAGE_NOT_IN_DESCRIPTION);
    }
}
