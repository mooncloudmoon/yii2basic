<?php
namespace app\modules\test\services\foundations;

use app\packages\Validate\ValidateService;
use Yii;
use app\modules\test\interfaces\TestInterface;

class FoundationService implements TestInterface
{
    use ValidateService;

    public $property1 = '';

    public function testFoundation($data)
    {
        $this->validateService($data, __CLASS__, __FUNCTION__);

        /** @var \app\modules\test\services\dependencies\DependencyService $dependencyService */
        $dependencyService = Yii::$container->get('DependencyService',['hello depencency']);
        $ret = $dependencyService->testDependency();
        return 'Foundation service which calls dependency service with return: ' . $ret . ', input data: ' . json_encode($data);
    }
}