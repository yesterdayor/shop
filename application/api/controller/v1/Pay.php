<?php
/**
 * Created by PhpStorm.
 * User: haipc
 * Date: 2019/11/12
 * Time: 1:04
 */

namespace app\api\controller\v1;


use app\api\validate\IDMustBePositiveInt;
use think\Controller;
use app\api\service\Pay as PayService;

class Pay extends Controller
{
    //中间件的绑定
    protected $middleware = [
        'check' => ['only' => ['getPreOrder']]
    ];

    /**
     * @param string $id
     * @return array
     * @throws \app\lib\exception\OrderException
     * @throws \app\lib\exception\ParamException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function getPreOrder($id = '')
    {
        (new IDMustBePositiveInt())->goCheck();
        $payService = new PayService($id);
        return $payService->pay();
    }
    public function receiveNotify()
    {
        //通知频率为15/30/180/1800/3600 单位：秒
        //1.检查库存量 ，超卖
        //2.更新订单状态，status
        //3.减库存
        //如果成功处理，我们返回微信成功处理的信息，否则，我们需要没有成功的处理
        //特点：post:xml格式：不会携带参数
    }

}