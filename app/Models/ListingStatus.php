<?php
declare(strict_types = 1);

namespace App\Models;

use App\Traits\ModelBuilderScopes;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property string name
 */
class ListingStatus extends Model
{
    const TYPE_INCOMPLETE = 1;
    const TYPE_AVAILABLE = 2;
    const TYPE_LISTED = 3;
    const TYPE_PENDING = 4;
    const TYPE_SOLD = 5;
    const TYPE_UNAVAILABLE = 6;

    use ModelBuilderScopes;

    protected $fillable = ['name'];

    protected $guarded = ['id'];

    public $timestamps = false;
}
