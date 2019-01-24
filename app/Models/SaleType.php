<?php
declare(strict_types = 1);

namespace App\Models;

use App\Traits\ModelBuilderScopes;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property string name
 */
class SaleType extends Model
{
    use ModelBuilderScopes;

    protected $fillable = ['name'];

    protected $guarded = ['id'];

    public $timestamps = false;
}
