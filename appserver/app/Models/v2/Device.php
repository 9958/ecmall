<?php
//
namespace App\Models\v2;

use App\Models\BaseModel;
use App\Helper\Token;
use App\Helper\Header;

class Device extends BaseModel {

    protected $connection = 'shop';
    protected $table      = 'device';
    public    $timestamps = true;
    protected $primaryKey = 'user_id';

    public static function toUpdateOrCreate($user_id, $data)
    {
        $device_id = Header::getUDID();
        if(is_null($device_id))
        {
            return false;
        }

        $userAgent = Header::getUserAgent();
        if (!$model = self::where('user_id', $user_id)->first()) {
            $model = new Device();
        }

        $model->user_id = $user_id;
        $model->device_id = $device_id;
        $model->platform_type = (isset($userAgent['Platform']) && $userAgent['Platform']) ? strtolower($userAgent['Platform']) : '';
        $model->device_type = (isset($userAgent['Device']) && $userAgent['Device']) ? strtolower($userAgent['Device']) : '';
        $model->status = 1;

        $model->save();
        $model->user_id = $user_id;

        return $model;
    }


   public static function updateDevice(array $attributes)
   {
        extract($attributes);
        $uid = Token::authorization();
        $userAgent = Header::getUserAgent();
        if ($device = Device::where('user_id', $uid)->delete())
        {

            $device = new Device;
            $device->user_id = $uid;
            $device->platform_type = (isset($userAgent['Platform']) && $userAgent['Platform']) ? strtolower($userAgent['Platform']) : '';
            $device->device_type = (isset($userAgent['Device']) && $userAgent['Device']) ? strtolower($userAgent['Device']) : '';
            $device->device_id = $device_id;

            if ($device->save())
            {
                $device->user_id = $uid;
                return self::formatBody(['device' => $device->toArray()]);

            }   else {

                return self::formatError(self::UNKNOWN_ERROR);
            }

        }else {

            return self::formatError(self::BAD_REQUEST);

        }
   }

}
