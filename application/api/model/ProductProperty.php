<?php
/**
 * Created by PhpStorm.
 * User: haipc
 * Date: 2019/11/6
 * Time: 12:30
 */

namespace app\api\model;


class ProductProperty extends BaseModel
{

    protected $hidden = ['product_id', 'delete_time', 'id', 'update_time'];
}