<?php
declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Share extends Model
{
    protected $fillable = ['entity_id', 'entity_type', 'network_id'];

    protected $guarded = ['id'];

    protected $hidden = ['created_at', 'updated_at', 'entity_id', 'entity_type'];


    /**
     * Get network name
     * @return BelongsTo
     */
    public function getNetwork()
    {
        return $this->belongsTo('App\Models\Network', 'network_id', 'id');
    }
}
