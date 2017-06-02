<?php
namespace app\modules\test\controllers;

use yii\web\Controller;
use Yii;

class TestController extends Controller
{
    public function actionTest()
    {
        //yInfo('this is a test action log message');
        /** @var \app\modules\test\services\foundations\FoundationService $foundationService */
        $foundationService = Yii::$container->get('FoundationService', [], ['property1' => 'aaa111']);
        $ret = $foundationService->testFoundation(['param1' => 'aaaa', 'param2' => 'bbbb', 'property' => $foundationService->property1]);
        echo $ret;
        exit;
    }
}