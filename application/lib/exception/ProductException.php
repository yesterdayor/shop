<?php
/**
 * Created by PhpStorm.
 * User: haipc
 * Date: 2019/10/31
 * Time: 16:43
 */

namespace app\lib\exception;


class ProductException extends BaseException
{
    public $code = 404;

    public $msg = '指定商品不存在，请检查参数';

    public $errorCode = 20000;
}