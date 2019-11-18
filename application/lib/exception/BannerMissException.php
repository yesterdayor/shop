<?php
/**
 * Created by PhpStorm.
 * User: haipc
 * Date: 2019/10/28
 * Time: 21:47
 */

namespace app\lib\exception;


class BannerMissException extends BaseException
{

    public $code = 400;

    public $msg = 'Banner不存在';

    public $errorCode = 40000;
}