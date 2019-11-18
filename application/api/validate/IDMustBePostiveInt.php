<?php
/**
 * Created by PhpStorm.
 * User: haipc
 * Date: 2019/10/28
 * Time: 3:29
 */

namespace app\api\validate;



class IDMustBePostiveInt extends  BaseValidate
{
    protected $rule = [
        'id' => 'require|isPositiveInteger',
    ];
    protected $message = [
        'id' => 'id必须是正整数'
    ];




}