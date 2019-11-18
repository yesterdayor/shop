<?php

namespace app\http\middleware;

use app\api\service\Token as TokenService;
use app\lib\enum\ScopeEnum;
use app\lib\exception\ForbiddenException;
use app\lib\exception\TokenException;

class Check
{
    public function handle($request, \Closure $next)
    {
//        $this->checkPrimaryScope();

        switch ($request->action()) {
            case 'createorupdateaddress':

                self::checkPrimaryScope();

                break;
            case 'placeorder' :
                self::needExclusiveScope();
                break;

        }

        return $next($request);

    }

    protected static function checkPrimaryScope()
    {
        $scope =TokenService::getCurrentTokenVar('scope');
        if ($scope) {
            if ($scope >= ScopeEnum::USER) {
                return true;
            } else {
                throw new ForbiddenException();
            }
        } else {
            throw new TokenException();
        }
    }

    protected static function needExclusiveScope()
    {
    $scope =TokenService::getCurrentTokenVar('scope');
    if ($scope) {
        if ($scope == ScopeEnum::USER) {
            return true;
        } else {
            throw new ForbiddenException(['msg' => '权限不符']);
        }
    } else {
        throw new TokenException();
    }
}
}
