<?php

namespace app\packages\Extend\Module;

use Yii;

class BasicDependencyModule extends Yii\base\Module
{
    public $provider;
    public $providerClass;

    public function init()
    {
        parent::init();

        //各个模块下的依赖
        BindModuleService::bind();
    }
}
