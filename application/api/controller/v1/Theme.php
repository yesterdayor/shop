<?php

namespace app\api\controller\v1;



use app\api\validate\IDCollection;
use app\api\model\Theme as ThemeModel;
use app\api\validate\IDMustBePostiveInt;
use app\lib\exception\ThemeException;

class Theme
{
    /**
     *
     *
     * @return array $list 一组theme模型
     */
    public function getSimpleList($ids='')
    {
        (new IDCollection())->goCheck();
        $result = ThemeModel::with('topicImg,headImg')->select();
        if (!$result) {
            throw new ThemeException();
        }
        return $result;
    }

    /**
     * @param $id
     * @url /theme/:id
     */
    public function getComplexOne($id)
    {
        (new IDMustBePostiveInt())->goCheck();

        $themes = ThemeModel::getThemeWithProducts($id);

        if ($themes->isEmpty()) {
            throw new ThemeException();
        }
        return $themes;

    }
}
