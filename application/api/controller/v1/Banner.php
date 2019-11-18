<?php
/**
 * Created by PhpStorm.
 * User: haipc
 * Date: 2019/10/27
 * Time: 4:25
 */

namespace app\api\controller\v1;



use app\api\model\Banner as BannerModel;
use app\api\validate\IDMustBePostiveInt;
use app\lib\exception\BannerMissException;

class Banner
{
    /**
     * @param $id
     * @url /banner/:id
     * @http get
     * @throws \think\Exception
     *
     */
    public function getBanner($id)
    {
        (new IDMustBePostiveInt())->goCheck();

//        $banner = BannerModel::with(['getBannerItems','getBannerItems.getImage'])->get($id); //结果是一个对象
        //get,find,select,allmy
       $banner = BannerModel::getBannerById($id);
       //$banner->hidden(['delete_time', 'update_time']);
       //$banner->visible();
       if ($banner->isEmpty()) {
           throw new BannerMissException();
       }
       return $banner;


    }
}