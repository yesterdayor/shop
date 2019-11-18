<?php
/**
 * Created by PhpStorm.
 * User: haipc
 * Date: 2019/11/7
 * Time: 19:08
 */

namespace app\lib\exception;


class ForbiddenException extends BaseException
{
    public $code = 403;
    public $msg = '权限不够';
    public $errorCode = 10001;
}