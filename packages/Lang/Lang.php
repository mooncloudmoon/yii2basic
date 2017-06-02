<?php

namespace app\packages\Lang;

use Yii;
use yii\base\Component;

class Lang extends Component
{
    protected $openLangPack = false;//是否开启识别语言包

    public function render($ret, $msg, $data, $is_json = true)
    {
        $ret = [
            'ret'  => $ret,
            'msg'  => $this->openLangPack ? $this->getLang($msg) : $msg,
            'data' => $data,
        ];

        return $is_json ? json_encode($ret) : $ret;
    }

    private function getLang($msg_key)
    {
        if (empty($msg_key)) {
            return '';
        }
        // 公用语言包目录
//        $publicCategory = 'frontend';
        // 错误码提示信息语言包目录
        $moduleCategory = "/lang";

        return Yii::t($moduleCategory, $msg_key);
    }
}