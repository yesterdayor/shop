<?php
/**
 * Created by PhpStorm.
 * User: haipc
 * Date: 2019/11/1
 * Time: 13:29
 */

namespace app\api\model;


class User extends BaseModel
{
    public  function address()
    {
        return $this->hasOne('UserAddress', 'user_id', 'id');


    }

    public static function getByOpenID($openID)
    {
        $user = self::where('openid', '=', $openID)->find();
        return $user;
    }
}