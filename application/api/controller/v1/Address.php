<?php
/**
 * Created by PhpStorm.
 * User: haipc
 * Date: 2019/11/6
 * Time: 19:43
 */

namespace app\api\controller\v1;


use app\api\model\User as UserModel;
use app\api\validate\AddressNew;
use app\api\service\Token as TokenService;
use app\lib\exception\UserException;
use think\Controller;


class Address extends Controller
{
    //中间件的绑定
    protected $middleware = [
            'check' => ['only' => ['createOrUpdateAddress']]
    ];

    public function createOrUpdateAddress()
    {
        $validate = new AddressNew();
        $validate->goCheck();

        //根据token获取uid
        //根据UID查询用户数据，判断用户是否存在，如果不存在，抛出异常
        //获取用户从客户端提交来的地址
        //根据用户
        $uid = TokenService::getCurrentUid();
        $user = UserModel::get($uid);
        if(!$user) {
            throw new UserException();
        }
        $dataArray = $validate->getDataByRule(input('post.'));

        $userAddress = $user->address;
        if (!$userAddress) {
            $user->address()->save($dataArray);
        }else {
            $user->address->save($dataArray);
        }
        return $user;

    }
}