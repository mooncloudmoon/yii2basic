<?php

namespace app\components;

use yii;
use yii\base\Component;

class RestComponent extends Component
{
    /**
     * 根据返回码获取对应的错误信息
     * @param int $ret 返回码
     * @param string $msg 指定错误信息
     * @param array $msgParams 语言包参数
     * @return string 错误信息
     */
    public function getErrorMsg($ret, $msg = '', $msgParams = [])
    {
        // 公用语言包目录
        $publicCategory = $moduleCategory = 'app';

        // 模块专属语言包目录
        if (yii::$app->controller) {
            $moduleCategory = $publicCategory . "/" . yii::$app->controller->module->id;
        }

        // 公用语言包优先
        if ($msg) {
            return $msg;
        } elseif (yii::t($publicCategory, $ret) !== $ret) {
            return yii::t($publicCategory, $ret, $msgParams);
        } else {
            return yii::t($moduleCategory, $ret, $msgParams);
        }
    }

    /**
     * 返回结果格式化，数组
     *
     * status 1成功 0失败
     * ret 0成功 非0失败
     * message
     * data
     *
     * @param int $ret 返回码
     * @param null $data 返回数据
     * @param string $msg 错误信息
     * @param array $msgParams 错误信息参数
     * @return array
     */
    public function output($ret, $data = null, $msg = "", $msgParams = [])
    {
        $status = self::getStatusByRet($ret);

        return [
            'status'  => $status,
            'ret'     => $ret,
            'message' => self::getErrorMsg($ret, $msg, $msgParams),
            'data'    => isset($data) && $data == null ? (object)$data : $data
        ];
    }

    /**
     * 通过ret返回成功失败的结果
     * status 1成功 0失败
     *
     * @param $ret
     * @return int
     */
    private function getStatusByRet($ret)
    {
        if ($ret === 0) {
            $status = 1;
        } else {
            $status = 0;
        }

        return $status;
    }
}