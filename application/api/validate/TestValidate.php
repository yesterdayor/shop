<?php
/**
 * Created by PhpStorm.
 * User: haipc
 * Date: 2019/10/28
 * Time: 2:57
 */

namespace app\api\validate;


use think\Validate;

class TestValidate extends Validate
{

    protected $rule = [
        'name' => 'require|max:10',
        'email' => 'email'
    ];
}