<?php
/**
 * Created by PhpStorm.
 * User: haipc
 * Date: 2019/11/8
 * Time: 15:16
 */

namespace app\api\service;


use app\api\model\OrderProduct as OrderProductModel;
use app\api\model\Product as ProductModel;
use app\api\model\UserAddress as UserAddressModel;
use app\api\model\Order as OrderModel;
use app\lib\exception\OrderException;
use app\lib\exception\UserException;
use think\Db;
use think\Exception;

class Order
{
    //订单的商品列表，也就是客户端传递过来的products参数
    protected $oProducts;

    //真实的商品信息
    protected $products;

    //用户id
    protected $uid;

    /**
     * @param $uid
     * @param $oProducts 订单产品数据
     * @return array
     * @throws Exception
     */
    public function place($uid, $oProducts)
    {
        //oProducts和products做对比
        //products从数据库中查询出来
        $this->oProducts = $oProducts;
        $this->uid = $uid;
        $this->products = $this->getProductsByOrder($oProducts);
        $status = $this->getOrderStatus();
        if (!$status['pass']) {
            $status['order_id'] = -1;
            return $status;
        }

        //开始创建订单
        $orderSnap = $this->snapOrder($status);
        $order = $this->createOrder($orderSnap);
        $order['pass'] = true;
        return $order;

    }

    /**
     * 创建订单
     * @param $snap
     * @return array
     * @throws Exception
     */
    private function createOrder($snap)
    {
        Db::startTrans();
        try {
            //创建订单编号
            $orderNo = self::makeOrderNo();
            $order = new OrderModel();
            $order->user_id = $this->uid;
            $order->order_no = $orderNo;
            $order->total_price = $snap['orderPrice'];
            $order->total_count = $snap['totalCount'];
            $order->snap_name = $snap['snapName'];
            $order->snap_img = $snap['snapImg'];
            $order->snap_address = $snap['snapAddress'];
            $order->snap_items = json_encode($snap['pStatus']);

            $order->save();
            $orderID = $order->id;
            $create_time = $order->create_time;
            foreach ($this->oProducts as &$p) {
                $p['order_id'] = $orderID;
            }
            $orderProduct = new OrderProductModel();
            $orderProduct->saveAll($this->oProducts);
            Db::commit();
            return [
                'order_no' => $orderNo,
                'order_id' => $orderID,
                'crate_time' => $create_time
                ];
        } catch (Exception $e) {
            Db::rollback();
            throw $e;
        }
    }


    /**
     *  //生成订单快照
     * @param $status
     */
    public function snapOrder($status)
    {
        $snap = [
            'orderPrice' => $status['orderPrice'],
            'totalCount' => $status['totalCount'],
            'pStatus' => $status['pStatusArray'],
            'snapAddress' => json_encode($this->getUserAddress()),
            'snapName' => $this->products[0]['name'],
            'snapImg' => $this->products[0]['main_img_url']
        ];
        if($this->products > 1) {
            $snap['snapName'] .= '等';
        }
        return $snap;
    }
    /**
     * 检查订单商品库存
     * @param $orderID
     * @return array
     */
    public  function checkOrderStock($orderID)
    {
        $oProducts = OrderProductModel::where('order_id', '=', $orderID)
            ->select();
        $this->oProducts = $oProducts;
        $this->products = $this->getProductsByOrder($oProducts);
        $status = $this->getOrderStatus();
        return $status;
    }
    /**
     * @param $oPID
     * @param $oCount
     * @param $products
     * @return array
     * @throws OrderException
     */
    private function getProductStatus($oPID, $oPCount, $products)
    {
        $pIndex = -1;
        $pStatus = [
            'id' => null,
            'haveStock' => false,
            'count' => 0,
            'name' => '',
            'totalPrice' => 0
        ];
        $count = count($products);
        for ($i = 0; $i < $count; $i ++) {
            if ($oPID == $products[$i]['id']) {
                $pIndex = $i;
                break;
            }
        }
        if ($pIndex == -1) {
            throw new OrderException(['msg' => 'id为' . $oPID .  '商品不存在，创建订单失败']);
        } else {
            $product = $products[$pIndex];
            $pStatus['id'] = $product['id'];
            $pStatus['name'] = $product['name'];
            $pStatus['count'] = $oPCount;
            $pStatus['totalPrice'] = $product['price'] * $oPCount;
            if ($product['stock'] - $oPCount >= 0 ) {
                $pStatus['haveStock'] = true;
            }
        }
        return $pStatus;
    }
    /**
     * 检查订单状态
     * @return array
     */
    private function getOrderStatus()
    {
        $status = [
            'pass' => true,
            'orderPrice' => 0,
            'totalCount' => 0,
            'pStatusArray' => []
        ];
        foreach ($this->oProducts as $oProduct) {
            $pStatus = $this->getProductStatus($oProduct['product_id'],
                $oProduct['count'], $this->products);
            if (!$pStatus['haveStock']) {
                $status['pass'] = false;
            }
            $status['orderPrice'] += $pStatus['totalPrice'];
            $status['totalCount'] += $pStatus['count'];
            array_push($status['pStatusArray'], $pStatus);
        }
        return $status;
    }
    /**
     * 根据订单信息查找真实的商品信息
     * @param $oProducts 前端传来的参数
     * @return array $prpduct
     * @throws \think\exception\DbException
     */
    public function getProductsByOrder($oProducts)
    {
        $array = array_column($oProducts, 'product_id');
        $products = ProductModel::all($array)
            ->visible(['id', 'price', 'stock', 'name', 'main_img_url'])
            ->toArray();
        return $products;
    }
    /**
     ******* 获取用户地址
     * @return array
     * @throws UserException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function  getUserAddress()
    {
        $userAddress = UserAddressModel::where('user_id', '=', $this->uid)
            ->find()->toArray();
        if(!$userAddress) {
            throw new UserException([
                'msg' => '用户收货地址不存在，下单失败',
                'errorCode' => 60001]);
        }
        return $userAddress;
    }
    /**
     * 订单号创建方法
     * @return string
     */
    public static function makeOrderNo()
    {
        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        $orderSn =
            $yCode[intval(date('Y')) - 2019] . strtoupper(dechex(date('m'))) . date(
                'd') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf(
                '%02d', rand(0, 99));
        return $orderSn;
    }
}