<?php
namespace app\packages\Extend\Test;

use Yii;
use app\packages\Extend\Module\BindModuleService;

class TestRelyBind extends \Codeception\Test\Unit
{
    /**
     * 绑定依赖
     */
    protected function _before()
    {
        parent::_before();
        //各个模块下的依赖
        BindModuleService::bind();
    }
}
