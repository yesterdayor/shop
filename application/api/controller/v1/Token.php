<?php
/**
 * Created by PhpStorm.
 * User: haipc
 * Date: 2019/11/1
 * Time: 13:23
 */

namespace app\api\controller\v1;


use app\api\validate\TokenGet;
use app\api\service\UserToken;

class Token
{
    public function getToken($code ='')
    {
        (new TokenGet())->goCheck();
        $ut = new UserToken($code);
        $token = $ut->get();
        return $token;
    }
}