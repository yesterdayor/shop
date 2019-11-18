<?php
/**
 * Created by PhpStorm.
 * User: haipc
 * Date: 2019/11/6
 * Time: 1:55
 */

namespace app\lib\exception;


class TokenException extends BaseException
{
    public $code = 401;
    public  $msg = 'Token无效或Token异常';
    public $errorCode = 10005;
}