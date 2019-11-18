<?php
/**
 * Created by PhpStorm.
 * User: haipc
 * Date: 2019/11/6
 * Time: 12:29
 */

namespace app\api\model;


class ProductImage extends BaseModel
{
    protected $hidden = ['img_id', 'delete_time', 'product_ud'];

    public function imgUrl()
    {
        return $this->belongsTo('Image','img_id', 'id');
    }

}