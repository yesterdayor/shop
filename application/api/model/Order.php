<?php
/**
 * Created by PhpStorm.
 * User: haipc
 * Date: 2019/11/10
 * Time: 18:01
 */

namespace app\api\model;


class Order extends BaseModel
{
    protected $hidden = ['user_id', 'delete_time', 'update_time'];

    protected $autoWriteTimestamp = true;
}