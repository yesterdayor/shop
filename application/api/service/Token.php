<?php
/**
 * Created by PhpStorm.
 * User: haipc
 * Date: 2019/11/6
 * Time: 1:33
 */

namespace app\api\service;


use app\lib\exception\TokenException;
use think\Exception;
use think\facade\Cache;
use think\facade\Request;

class Token
{
    public static function generateToken()
    {
        //32位随机字符串
        $randCached = getRandChar(32);

        //用三组字符串，进行MD5加密
        $timestamp = $_SERVER['REQUEST_TIME_FLOAT'];
        //salt 盐
        $salt = config('secure.token_salt');

        return md5($randCached . $timestamp . $salt);
    }

    public static function getCurrentTokenVar($key)
    {
        $token = Request::instance()->header('token');
        $vars = Cache::get($token);
        if (!$vars) {
            throw new TokenException();
        } else {
            //容错处理
            if(!is_array($vars)) {
                $vars = json_decode($vars, true);
            }
            if (array_key_exists($key, $vars)) {
                return $vars[$key];
            } else {
                throw new Exception('尝试获取的token变量不存在');
            }

        }
    }

    /**
     * @return uid
     * @throws Exception
     * @throws TokenException
     */
    public static function getCurrentUid()
    {
        //token
        return self::getCurrentTokenVar('uid');
    }

    /**
     * @param $checkUID
     * @return bool
     * @throws Exception
     * @throws TokenException
     * @throws \app\lib\exception\ParameterException
     */
    public static function isValidOperate($checkUID)
    {
        if (!$checkUID) {
            throw new Exception('检查UID，必须传入一个被检查的UID');
        }
        $uid = self::getCurrentUid();
        if ($uid == $checkUID) {
            return true;
        }
        return false;
    }
}
