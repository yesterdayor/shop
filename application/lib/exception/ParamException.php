<?php
/**
 * Created by PhpStorm.
 * User: haipc
 * Date: 2019/10/29
 * Time: 0:20
 */

namespace app\lib\exception;


use Throwable;

class ParamException extends BaseException
{
    public $code = 400;

    public $msg = '参数错误';

    public $errorCode = 10002;


}