<?php
declare(strict_types = 1);

namespace App\Models;

use App\Traits\ModelBuilderScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int id
 * @property int listing_id
 * @property int price
 * @property int sale_type
 * @property int monthly_payment
 * @property int percentage_rate
 * @property int financial_term
 * @property int taxes
 */
class ListingPrice extends Model
{
    use ModelBuilderScopes;

    protected $fillable = [
        'listing_id', 'price', 'sale_type', 'monthly_payment', 'percentage_rate', 'financial_term', 'taxes',
    ];

    protected $guarded = ['id'];

    protected $hidden = ['created_at', 'updated_at', 'listing_id'];


    /**
     * Get listing sale type
     * @return BelongsTo
     */
    public function getSaleType()
    {
        return $this->belongsTo('App\Models\SaleType', 'sale_type', 'id');
    }
}
