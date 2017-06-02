<?php
namespace app\modules\test\configs;

use Yii;

class Provider
{
    /**
     * 依赖处理
     */
    public function bind()
    {
        Yii::$container->set('FoundationService', 'app\modules\test\services\foundations\FoundationService');
        Yii::$container->set('DependencyService', 'app\modules\test\services\dependencies\DependencyService');
    }
}