<?php
declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property int listing_id
 * @property string size_type
 * @property string state
 * @property string county
 * @property string city
 * @property string address
 * @property double longitude
 * @property double latitude
 */
class ListingGeo extends Model
{
    protected $fillable = [
        'listing_id', 'size_type', 'state', 'county', 'city', 'address', 'longitude', 'latitude'
    ];

    protected $guarded = ['id'];

    protected $hidden = ['created_at', 'updated_at', 'listing_id'];


    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $fields
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereFields($query, array $fields)
    {
        $params = collect($fields)->filter(function ($value, $key) {
            return in_array($key, $this->fillable);
        })->all();

        return $query->where($params);
    }

}
