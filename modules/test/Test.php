<?php
namespace app\modules\test;

use app\packages\Extend\Module\BasicDependencyModule;

class Test extends BasicDependencyModule
{
    public $controllerNamespace = 'app\modules\test\controllers';

    public function init()
    {
        parent::init();
    }
}