<?php
declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property int entity_id
 * @property int entity_type
 * @property int number
 */
class Telephone extends Model
{
    const TYPE_USER_PHONE = 1;
    const TYPE_USER_FAX = 2;
    const TYPE_USER_TOLL_FREE = 3;
    const TYPE_SELLER = 4;

    protected $fillable = ['entity_id', 'entity_type', 'number'];

    protected $guarded = ['id'];

    protected $hidden = ['created_at', 'updated_at', 'entity_id', 'entity_type'];


    /**
     * @param string $type
     * @return int
     */
    static function checkType(string $type)
    {
        switch ($type) {
            case 'phone':
                return self::TYPE_USER_PHONE;
                break;
            case 'fax':
                return self::TYPE_USER_FAX;
                break;
            case 'toll_free':
                return self::TYPE_USER_TOLL_FREE;
                break;
            case 'seller':
                return self::TYPE_SELLER;
                break;
        }
    }
}
