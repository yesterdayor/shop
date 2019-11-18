<?php
/**
 * Created by PhpStorm.
 * User: haipc
 * Date: 2019/11/1
 * Time: 1:59
 */

namespace app\api\model;


class Category extends BaseModel
{
    protected  $hidden = ['delete_time', 'update_time'];
    public function img()
    {
        return $this->belongsTo('Image', 'topic_img_id', 'id');
    }
}