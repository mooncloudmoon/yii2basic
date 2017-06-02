<?php

/**
 * Created by PhpStorm.
 * User: yyuan
 * Date: 2017/3/27
 * Time: 18:37
 */
namespace app\packages\CommonRegister;

use yii;

class CommonRegister
{
    public static $objects;

    /**
     * 插入对象
     * @param $alias 对象别名
     * @param $object 对象实例
     */
    public static function set($alias, $object)
    {
        self::$objects[$alias] = $object;
    }

    public static function get($alias)
    {
        if (isset(self::$objects[$alias])) {
            return self::$objects[$alias];
        } else {
            return;
        }

    }

    /**
     * 取出对象
     * @param $alias 对象别名
     */
    public static function _unset($alias)
    {
        unset(self::$objects[$alias]);
    }
}