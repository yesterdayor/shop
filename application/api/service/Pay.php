<?php
/**
 * Created by PhpStorm.
 * User: haipc
 * Date: 2019/11/12
 * Time: 1:14
 */

namespace app\api\service;


use app\lib\enum\OrderStatusEnum;
use app\lib\exception\OrderException;
use app\lib\exception\TokenException;
use think\Exception;
use app\api\service\Order as OrderService;
use app\api\model\Order as OrderModel;
use think\facade\Env;
use think\facade\Log;

require_once Env::get('ROOT_PATH') . 'extend/wx_pay/WxPay.Api.php';

class Pay
{
    private $orderID;

    private $orderNo;

    public function __construct($orderID)
    {
        if (!$orderID) {
            throw new Exception('订单id不能为空');
        }
        $this->orderID = $orderID;
    }

    /**
     * @return array
     * @throws Exception
     * @throws OrderException
     * @throws TokenException
     */
    public function pay()
    {
        //订单号可能不存在
        //订单号存在，但与当前用户不匹配
        //订单有可能已经支付过
        //进行库存检测
        $this->checkOrderValid();
        $orderService = new OrderService();
        $status = $orderService->checkOrderStock($this->orderID);
        if (!$status['pass']) {
            return $status;
        }
        return $this->makeWxPreOrder($status['totalPrice']);
    }

    /**
     *   生成预支付订单
     * @param $totalPrice
     * @return array
     * @throws Exception
     * @throws TokenException
     * @throws \WxPayException
     */
    private function makePreOrder($totalPrice)
    {
        //openid
        $openid = Token::getCurrentTokenVar('openid');
        if (!$openid) {
            throw new TokenException();
        }
        $wxOrderData = new \WxPayUnifiedOrder();
        $wxOrderData->SetOut_trade_no($this->orderNo);
        $wxOrderData->SetTrade_type('JSAPI');
        $wxOrderData->SetTotal_fee($totalPrice * 100);
        $wxOrderData->SetBody('零食商贩');
        $wxOrderData->SetOpenid($openid);
        $wxOrderData->SetNotify_url('http://qq.com');
        return $this->getPaySignature($wxOrderData);
    }

    /**
     * @param $wxOrderDate
     * @return array
     * @throws \WxPayException
     */
    private function getPaySignature($wxOrderDate)
    {
        $wxOrder = \WxPayApi::unifiedOrder($wxOrderDate);
        if ($wxOrder['return_code'] != 'SUCCESS' || $wxOrder['result_code'] != 'SUCCESS') {
            Log::record($wxOrder, 'error');
            Log::record('获取预支付订单失败', 'error');
        }
        //$prepay_id
        $this->recordPreOrder($wxOrder);
        $signature = $this->sign($wxOrder);
        return $signature;
    }

    /**
     * @param $wxOrder
     * @return array
     * @throws \WxPayException
     */
    private function sign($wxOrder)
    {
        $jsApiPayData = new \WxPayJsApiPay();
        $jsApiPayData->SetAppid(config('wx.app_id'));
        $jsApiPayData->SetTimeStamp((string)time());
        $rand = md5(time() . mt_rand(0, 100));
        $jsApiPayData->SetNonceStr($rand);
        $jsApiPayData->SetPackage('prepay_id=' . $wxOrder['prepay_id']);
        $jsApiPayData->SetSignType('md5');
        $sign = $jsApiPayData->MakeSign();
        $rawValues = $jsApiPayData->GetValues();
        $rawValues['paySign'] = $sign;
        unset($rawValues['appId']);
        return $rawValues;
    }

    /**
     * 更新prepay_id 预支付id
     * @param $wxOrder
     * @throws Exception
     * @throws \think\exception\PDOException
     */
    private function recordPreOrder($wxOrder)
    {
        orderModel::when('id', '=', $this->orderID)
            ->update(['prepay_id' => $wxOrder['prepay_id']]);
    }
    /**
     * 检查订单信息详情detail
     * @return bool
     * @throws OrderException
     * @throws TokenException
     */
    private function checkOrderValid()
    {
        //检查订单是否存在
        $order = OrderModel::where('order_id', '=', $this->orderID)->find();
        if (!$order) {
            throw new OrderException();
        }
        //检查:订单号存在，但与当前用户不匹配
        if (!Token::isValidOperate($order->user_id)) {
            throw new TokenException(['msg' => '订单与用户不匹配!', 'errorCode' => 10003]);
        }
        //检查订单状态
        if ($order->status != OrderStatusEnum::UNPAID) {
            throw new OrderException([
                'msg' => '订单已支付过了!',
                'code' => 400,
                'errorCode' => 80003
            ]);
        }
        $this->orderNo = $order->order_no;

        return true;
    }
}