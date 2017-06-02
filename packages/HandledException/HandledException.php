<?php
namespace app\packages\HandledException;

use Yii;

/**
 * Created by PhpStorm.
 * User: yyuan
 * Date: 2017/4/6
 * Time: 15:19
 */
class HandledException extends \Exception
{
    public function __construct($code = -1, $message = "", \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function outPut()
    {
        $msgCode = $this->code;

        return yii::$app->rest->output($msgCode);
    }
}