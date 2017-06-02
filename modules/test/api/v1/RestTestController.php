<?php
namespace app\modules\test\api\v1;

use app\controllers\RestController;
use Yii;
use Exception;

class RestTestController extends RestController
{
    public function init()
    {
        parent::init();
        $this->noAuthActionArr = [
            'rest-test-without-auth',
        ];
    }

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs['rest-test'] = ['GET'];
        return $verbs;
    }

    public function actionRestTest($param)
    {
        try {
            yInfo('this is a test action log message');
            /** @var \app\modules\test\services\foundations\FoundationService $foundationService */
            $foundationService = Yii::$container->get('FoundationService');
            $ret               = $foundationService->testFoundation(['param2' => 'cccc']);

            return Yii::$app->rest->output(0, ['use auth', $ret, 'param' => $param], 'success');
        } catch (Exception $e) {
            return Yii::$app->rest->output(1006, [], $e->getMessage());
        }
    }
    
    public function actionRestTestWithoutAuth()
    {
        return Yii::$app->rest->output(10000,['method without auth']);
    }
}