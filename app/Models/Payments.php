<?php
declare(strict_types = 1);

namespace App\Models;

use App\Traits\ModelBuilderScopes;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Payments
 * @package App\Models
 */
class Payments extends Model
{
    use ModelBuilderScopes;

    protected $fillable = [
        'status',
        'user_id',
        'listing_id',
        'transaction_id',
        'price',
        'total_price',
    ];

    protected $guarded = ['id'];

    protected $hidden = ['created_at', 'updated_at'];

    public $timestamps = true;
}
