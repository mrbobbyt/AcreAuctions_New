<?php
declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int id
 * @property string email
 * @property string token
 */
class RegisterToken extends Model
{
    protected $fillable = ['email', 'token'];

    protected $hidden = ['id'];

    public $timestamps = false;

    /**
     * Get belongs user model
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'email', 'email');
    }
}
