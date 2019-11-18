<?php
/**
 * Created by PhpStorm.
 * User: haipc
 * Date: 2019/11/8
 * Time: 4:19
 */

namespace app\api\controller\v1;
use app\api\service\Order as OrderService;


use app\api\validate\OrderPlace;
use think\Controller;
use app\api\service\Token as TokenService;

class Order extends Controller
{

    //中间件的绑定
    protected $middleware = [
        'check' => ['only' => ['placeOrder']]
    ];
    //用户选择商品后，向API提交包含它所选择商品的相关信息
    ///API在接收到信息后，需要检查订单相关商品的库存量
    //有库存，把订单数据写入数据库中=下单成功。返回client信息，告诉client可以支付
    //调用支付接口，进行支付
    //还需要进行库存检测
    //服务器调用微信的支付接口检测
    //微信返回支付结果（异步）
    //成功：也需要进行库存检测
    //成功，进行库存量减少

    public function placeOrder()
    {
        (new OrderPlace())->goCheck();
        $oProducts = input('post.products/a');
        $uid = TokenService::getCurrentTokenVar('uid');
        $order = new OrderService();
        return json_encode($order ->place($uid, $oProducts));

    }
}