<?php
/**
 * Created by PhpStorm.
 * User: haipc
 * Date: 2019/11/6
 * Time: 21:10
 */

namespace app\api\model;


class UserAddress extends BaseModel
{
    protected $hidden = ['id', 'delete_time', 'user_id'];

}