<?php
/**
 * Created by PhpStorm.
 * User: haipc
 * Date: 2019/10/31
 * Time: 16:31
 */

namespace app\api\validate;


class Count extends BaseValidate
{
    protected $rule = [
        'count' => 'isPositiveInteger|between:1,15'
    ];
}