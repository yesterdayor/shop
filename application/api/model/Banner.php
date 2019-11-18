<?php

namespace app\api\model;

class Banner extends BaseModel
{
    protected $hidden = ['delete_time', 'update_time'];
    //
    /**
     * @param $id
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function  getBannerById($id)
    {
        $result = self::with(['getBannerItems','getBannerItems.getImage'])->get($id);
        return $result;

    }

    /**
     * @return \think\model\relation\HasMany
     */
    public function getBannerItems()
    {
        return $this->hasMany('BannerItem', 'banner_id','id');
    }
}
