<?php

namespace app\packages\Extend\Module;

use app\models\User;
use Yii;
use app\packages\Http\Http;

class BindModuleService
{

    //获取各个模块下的依赖
    public static function bind()
    {
        self::bindAllModules();
        self::bindBasic();
    }

    public static function bindAllModules()
    {
        //可以继承此类的模块
        $modules       = ['debug', 'gii'];
        $array_modules = Yii::$app->modules;
        foreach ($array_modules as $k => $array_module) {
            if (!in_array($k, $modules) && array_key_exists('providerClass', $array_module)) {
                try {
                    $provider = $array_module['providerClass'];
                    (new $provider())->bind();
                } catch (Exception $e) {
                    throw new Exception('模块依赖加载失败');
                }
            }
        }
    }

    //获取公共的依赖
    private static function bindBasic()
    {
        Yii::$container->setSingleton('Http', new Http());
        Yii::$container->setSingleton('UserAuth', new User());
    }
}
