<?php

namespace app\packages\Validate;

use Yii;
use yii\validators\BooleanValidator;
use yii\validators\CompareValidator;
use yii\validators\EmailValidator;
use yii\validators\NumberValidator;
use yii\validators\RangeValidator;
use yii\validators\RegularExpressionValidator;
use yii\validators\RequiredValidator;
use yii\validators\StringValidator;
use yii\validators\UrlValidator;

class ValidateFunctions
{
    private $error = '';

    //验证是否为空
    public function notEmpty($val)
    {
        $requiredValidator = new RequiredValidator();
        $res               = $requiredValidator->validate($val, $this->error);

        return $res;
    }

    //验证是否为邮箱
    public function isEmail($val)
    {
        $emailValidator = new EmailValidator();
        $res = $emailValidator->validate($val, $this->error);

        return $res;
    }

    //验证是否为手机号码
    public function isMobile($val)
    {
        $pattern         = [
            'pattern' => '/^1(3|4|5|7|8)\d{9}$/',
        ];
        $mobileValidator = new RegularExpressionValidator($pattern);
        $res             = $mobileValidator->validate($val, $this->error);

        return $res;
    }

    //验证是否为整数
    public function isInteger($val)
    {
        $integerValidator = new NumberValidator(['integerOnly' => true]);
        $res = $integerValidator->validate($val, $this->error);

        return $res;
    }

    //验证是否为数字
    public function isNumber($val)
    {
        $numberValidator = new NumberValidator(['integerOnly' => false]);
        $res = $numberValidator->validate($val, $this->error);

        return $res;
    }

    //验证是否为double,int类型也属于double
    public function isDouble($val)
    {
        $pattern         = [
            'pattern' => '/^[-\+]?\d+(\.\d+)?$/',
        ];
        $mobileValidator = new RegularExpressionValidator($pattern);
        $res             = $mobileValidator->validate($val, $this->error);

        return $res;
    }

    //验证是否为字符串
    public function isString($val)
    {
        $stringValidator = new StringValidator();
        $res = $stringValidator->validate($val, $this->error);

        return $res;
    }

    //验证是否为布尔型
    public function isBoolean($bool)
    {
        $error            = '';
        $booleanValidator = new BooleanValidator();
        $res              = $booleanValidator->validate($bool, $error);

        return $res;
    }

    //最大长度
    public function maxLen($val, $len)
    {
        $stringValidator = new StringValidator(['max' => $len]);
        $res = $stringValidator->validate($val, $this->error);

        return $res;
    }

    //最小长度
    public function minLen($val, $len)
    {
        $stringValidator = new StringValidator(['min' => $len]);
        $res = $stringValidator->validate($val, $this->error);

        return $res;
    }

    //max验证
    public function max($val, $max)
    {
        $maxValidator = new CompareValidator(['compareValue' => $max, 'operator' => '<=']);
        $res = $maxValidator->validate($val, $this->error);

        return $res;
    }

    //min验证
    public function min($val, $min)
    {
        $minValidator = new CompareValidator(['compareValue' => $min, 'operator' => '>=']);
        $res = $minValidator->validate($val, $this->error);

        return $res;
    }

    //区间
    public function between($val, $between)
    {
        $minValidator = new CompareValidator(['compareValue' => $between[0], 'operator' => '>=']);
        $resMin = $minValidator->validate($val, $this->error);

        $maxValidator = new CompareValidator(['compareValue' => $between[1], 'operator' => '<=']);
        $resMax = $maxValidator->validate($val, $this->error);

        return ($resMin & $resMax);
    }

    //范围
    public function range($val, $range)
    {
        $rangeValidator = new RangeValidator(['range' => $range]);
        $res = $rangeValidator->validate($val, $this->error);

        return $res;
    }

    //url验证
    public function isUrl($val)
    {
        $urlValidator = new UrlValidator();
        $res = $urlValidator->validate($val, $this->error);

        return $res;
    }
}