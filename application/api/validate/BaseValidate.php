<?php
/**
 * Created by PhpStorm.
 * User: haipc
 * Date: 2019/10/28
 * Time: 3:55
 */

namespace app\api\validate;


use app\lib\exception\ParamException;
use think\facade\Request;
use think\Validate;

class BaseValidate extends  Validate
{
    /**
     * @return bool
     * @throws ParamException
     */
    public function goCheck()
    {
        //获取http传入的参数
        //对这些参数进行校验
        $request = Request::instance();
        $params = $request->param();
        $result = $this->batch()->check($params);
        if (!$result) {
            $e = new ParamException([
                'msg' => $this->error,
//                'code' => 400,
//                'errorCode' => 10002
            ]);
            throw $e;
            //$error = $this->error;
            //throw new Exception($error);
        } else {
            return true;
        }
    }

    /**
     * 检查参数是否是正整数
     * @param $value
     * @param string $rule
     * @param string $data
     * @param string $field
     * @return bool|string
     */
    protected  function  isPositiveInteger($value, $rule = '', $data = '', $field = '')
    {
        if (is_numeric($value) && is_int($value + 0) && ($value + 0) > 0) {

            return true;
        } else {
//            return $field . '必须是正整数';
            return false;
        }
    }

    /**
     * 验证是否为空
     * @param $value
     * @param string $rule
     * @param string $data
     * @param string $field
     * @return bool
     */
    protected function isNotEmpty($value, $rule = '', $data = '', $field = '')
    {
        if (empty($value)) {
            return false;
        }
        return true;
    }

    /**
     * @param $arrays
     * @return array
     * @throws ParamException
     */
    public function getDataByRule($arrays)
    {
        if(array_key_exists('user_id',$arrays) | array_key_exists('uid', $arrays)) {
            throw new ParamException(['msg' => '参数中包含非法的参数名user_id或uid']);
        }
        $newArray = [];
        foreach ($this->rule as $key =>$value) {
            $newArray[$key] = $arrays[$key];
        }
        return $newArray;
    }

    /**
     * 验证手机号
     * @param $value
     * @return bool
     */
    protected function isMobile($value)
    {
        $rule = "/^1(3|4|5|7|8)[0-9]\d{8}$/";
        $result = preg_match($rule, $value);
        if ($result) {
            return true;
        }
        return false;
    }
}