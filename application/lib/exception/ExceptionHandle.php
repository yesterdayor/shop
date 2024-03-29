<?php
/**
 * Created by PhpStorm.
 * User: haipc
 * Date: 2019/10/28
 * Time: 21:42
 */

namespace app\lib\exception;

use think\exception\Handle;
use think\facade\Request;
use think\facade\Log;

class ExceptionHandle extends Handle
{
    private $code;

    private $msg;

    private $errorCode;

    //需要返回客户端的当前请求的url地址


    /**
     * @param \Exception $e 参数类型一定是 PHP自带异常处理类 Exception 不能是think\Exception类
     * @return \think\Response|\think\response\Json
     */
    public function render(\Exception $e)
    {
//      return parent::render($e); // TODO: Change the autogenerated stub
        if ($e instanceof BaseException) {
            //如果是自定义的异常
            $this->code = $e->code;
            $this->msg = $e->msg;
            $this->errorCode = $e->errorCode;
        } else {
            if(config('app_debug')){
                return parent::render($e);
            }
            $this->code = 500;
            $this->msg = '服务器内部错误，开发人员太水了';
            $this->errorCode = 999;
            $this->recordErrorLog($e);
        }
        $request = Request::instance();
        $result = [
            'msg' => $this->msg,
            'error_code' => $this->errorCode,
            'request_url' => $request->url()
        ];

        return json($result, $this->code);
    }

    private function recordErrorLog(\Exception $e)
    {
        Log::record($e->getMessage(), 'error');
    }
}









