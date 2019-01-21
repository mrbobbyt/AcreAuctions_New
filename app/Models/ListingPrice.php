<?php
declare(strict_types = 1);

namespace App\Models;

use App\Traits\ModelBuilderScopes;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property int listing_id
 * @property int price
 * @property int monthly_payment
 * @property int processing_fee
 * @property int percentage_rate
 * @property int financial_term
 * @property int taxes
 */
class ListingPrice extends Model
{
    use ModelBuilderScopes;

    protected $fillable = [
        'listing_id', 'price', 'monthly_payment', 'processing_fee', 'percentage_rate', 'financial_term', 'taxes',
    ];

    protected $guarded = ['id'];

    protected $hidden = ['created_at', 'updated_at', 'listing_id'];

}
