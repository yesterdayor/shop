<?php

namespace app\api\model;

class Image extends BaseModel
{
    //隐藏字段
    protected $hidden = ['id', 'from', 'delete_time', 'update_time'];

    //图片路径读取器
    public function getUrlAttr($value, $data)
    {
//        $finalUrl = $value;
//        if ($data['from'] == 1) {
//            $finalUrl =  config('setting.img_prefix') . $value;
//        }
//        return $finalUrl;
        return $this->prefixImageUrl($value, $data);
    }
}
