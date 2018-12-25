<?php
declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property string title
 * @property string subtitle
 * @property string slug
 * @property string description
 * @property bool is_featured
 * @property int seller_id
 */
class Listing extends Model
{
    protected $fillable = [
        'title', 'subtitle', 'slug', 'description', 'is_featured', 'seller_id'
    ];

    protected $guarded = ['id'];

}
