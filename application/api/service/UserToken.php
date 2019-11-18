<?php
/**
 * Created by PhpStorm.
 * User: haipc
 * Date: 2019/11/1
 * Time: 13:30
 */
namespace app\api\service;



use app\api\model\User;
use app\lib\enum\ScopeEnum;
use app\lib\exception\TokenException;
use app\lib\exception\WeChatException;
use app\api\model\User as UserModel;

class UserToken extends Token
{
    protected $code;
    protected $wxAppId;
    protected $wxAppSecret;
    protected $wxLoginUrl;

    public function __construct($code)
    {
        $this->code = $code;
        $this->wxAppId = config('wx.app_id');
        $this->wxAppSecret = config('wx.app_secret');
        $this->wxLoginUrl = sprintf(config('wx.login_url'),
            $this->wxAppId, $this->wxAppSecret, $this->code);
    }

    public function get()
    {
        $result = curl_get($this->wxLoginUrl);
        $wxResult = json_decode($result, true);
        if(empty($wxResult)) {
            throw new Exception('获取session_key及open_id时异常，微信内部错误');
        } else {
            $loginFail = array_key_exists('errcode', $wxResult);
            if ($loginFail) {
                $this->processLoginError($wxResult);
            } else {
               $token = $this->grantToken($wxResult);
                return $token;
            }
        }
    }

    private function grantToken($wxResult)
    {
        //拿到openID
        //数据库查看openid,这个openID是否存在
        //如果不存在信泽一条user记录，如果存在，则不处理
        //生成令牌，准备缓存数据，写入缓存
        //把令牌返回客户端
        $openID = $wxResult['openid'];
        $user = UserModel::getByOpenID($openID);
        if ($user) {
            $uid = $user->id;
        } else {
            $uid = $this->newUser($openID);
        }
        $cachedValue = $this->prepareCachedValue($wxResult, $uid);
        $token = $this->saveToCached($cachedValue);
        return $token;
    }

    //存缓存
    private function saveToCached($cachedValue)
    {
        $key = self::generateToken();
        $value = json_encode($cachedValue);
        $tokenExpireTime = config('secure.token_expire_time');
        $request = cache($key, $value, $tokenExpireTime);
        if (!$request) {
            throw new TokenException(['msg' => '缓存服务异常', 'errCode' => 10005]);
        }
        return $key;
    }
    //缓存内容
    private function prepareCachedValue($wxResult, $uid)
    {
        $cachedValue = $wxResult;
        $cachedValue['uid'] = $uid;
        //scope=16代表app用户的权限数值
        //scope =32 代表cms(管理员）用户全权限数值
        $cachedValue['scope'] = ScopeEnum::USER;

        return $cachedValue;
    }
    //新增数据 user
    private function newUser($openID)
    {
        $user = UserModel::create(['openid' => $openID]);
        return $user->id;
    }

    //微信错误码
    private function processLoginError($wxResult)
    {
        throw new WeChatException(
            [
                'msg' => $wxResult['errmsg'],
                'errorCode' => $wxResult['errcode']
            ]);
    }

}