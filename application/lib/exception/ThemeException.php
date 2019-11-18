<?php
/**
 * Created by PhpStorm.
 * User: haipc
 * Date: 2019/10/31
 * Time: 15:07
 */

namespace app\lib\exception;


class ThemeException extends BaseException
{
    public $code =404;

    public $msg = '指定主题不存在，请检查主题ID';

    public $errorCode = 30000;

}