<?php
declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string email
 * @property string token
 */
class PasswordResets extends Model
{
    const EMAIL_REASON = 'forgot';

    protected $fillable = ['email', 'token', 'created_at'];


    /**
     * Get belongs user model
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'email', 'email');
    }

}
