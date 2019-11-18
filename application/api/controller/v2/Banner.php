<?php
/**
 * Created by PhpStorm.
 * User: haipc
 * Date: 2019/10/27
 * Time: 4:25
 */

namespace app\api\controller\v2;



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
    public function getBanner()
    {
       return 'This is version 2';


    }
}