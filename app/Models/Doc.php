<?php
declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int id
 * @property int entity_id
 * @property int entity_type
 * @property string name
 * @property string desc
 */
class Doc extends Model
{
    const TYPE_LISTING = 1;

    protected $fillable = ['entity_id', 'entity_type', 'name', 'desc'];

    protected $guarded = ['id'];

    protected $hidden = ['created_at', 'updated_at', 'entity_id', 'entity_type', 'name', 'getListing'];

    protected $appends = ['full_path'];


    /**
     * Get seller
     * @return BelongsTo
     */
    public function getListing()
    {
        return $this->belongsTo('App\Models\Listing', 'entity_id', 'id');
    }


    /**
     * @return string
     */
    public function getFullPathAttribute()
    {
        return get_doc_path($this->getListing->id, $this->name);
    }
}
