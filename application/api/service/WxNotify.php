<?php


namespace app\api\service;

use think\facade\Env;

require_once Env::get('ROOT_PATH') . 'extend/wx_pay/WxPay.Notify.php';

class WxNotify extends \WxPayNotify
{
    public function NotifyProcess($objData, $config, &$msg)
    {

    }
}