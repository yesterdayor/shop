<?php
/**
 * Created by PhpStorm.
 * User: haipc
 * Date: 2019/11/9
 * Time: 0:23
 */

namespace app\lib\exception;


class OrderException extends BaseException
{
    public $msg = '订单不存在，请检查ID';

    public $code = 404;

    public $errorCode = 80000;
}