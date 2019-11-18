<?php

namespace app\api\model;



class BannerItem extends baseModel
{
    protected $hidden = ['id','img_id', 'banner_id', 'delete_time', 'update_time'];

    //
    public function getImage()
    {
        return $this->belongsTo('Image','img_id', 'id');
    }
}
