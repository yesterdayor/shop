<?php
/**
 * Created by PhpStorm.
 * User: haipc
 * Date: 2019/11/6
 * Time: 20:27
 */

namespace app\lib\exception;


class UserException extends BaseException
{
    public $code = 404;

    public $msg = '用户不存在';

    public $errorCode = 40000;
}