<?php
/**
 * Created by PhpStorm.
 * User: haipc
 * Date: 2019/10/30
 * Time: 23:06
 */

namespace app\api\validate;


class IDCollection extends BaseValidate
{
    protected $rule = [
        'ids' => 'require|checkIDs'
    ];
    protected $message = [
        'ids' => 'ids必须是以逗号分隔的多个正整数'
    ];

    protected function checkIDs($value, $rule = '', $data= '', $field = '')
    {
        $values = explode(',', $value);

        if (empty($values)){
            return false;
        }
        foreach ($values as $id) {
            if (!$this->isPositiveInteger($id)) {
                return false;
            }
        }
        return true;
    }
}