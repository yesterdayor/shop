<?php
/**
 * Created by PhpStorm.
 * User: haipc
 * Date: 2019/11/1
 * Time: 2:11
 */

namespace app\lib\exception;


class CategoryException extends BaseException
{
    public $code = 404;

    public $msg = '指定类目不存在, 请检查参数';

    public $errorCode = 50000;
}