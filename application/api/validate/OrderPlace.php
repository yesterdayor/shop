<?php
/**
 * Created by PhpStorm.
 * User: haipc
 * Date: 2019/11/8
 * Time: 14:50
 */

namespace app\api\validate;


use app\lib\exception\ParamException;


class OrderPlace extends BaseValidate
{
    //总验证规则
    protected $rule = [
      'products' => 'checkProducts'
    ];

    //单个商品的验证规则
    protected $singleRule = [
        'product_id' => 'require|isPositiveInteger',
        'count' => 'require|isPositiveInteger'
    ];

    /**
     * 验证订单商品数据的正正确性
     * @param $values
     * @return bool
     * @throws ParamException
     */
    protected function checkProducts($values)
    {
        if (empty($values)) {
            throw new ParamException(['msg' => '商品列表不能为空']);
        }
        if (!is_array($values)) {
            throw new ParamException(['msg' => '参数不正确']);
        }
        foreach ($values as $value) {
            $this->checkSingle($value);
        }
        return true;
    }

    /**
     * 检查单个商品信息
     * @param $value
     * @throws ParamException
     */
    protected function checkSingle($value)
    {
        $validate = new self($this->singleRule);
        $result = $validate->check($value);
        if (!$result) {
            throw new ParamException(['msg' => '商品列表参数错误']);
        }
    }
}