<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\packages\Validate;

use Yii;
use yii\helpers\HtmlPurifier;
use yii\web\Response;

class ValidatorTools
{
    private $params = array();
    static private $validatorTool;
    static private $regexTool = null;
    static private $validateConfig = null;
    static private $routeKey;
    private $errs = array();

    /*private function __construct()
    {
        self::$regexTool = new RegexTool();
        if (isset(Yii::$app->params['validate'])) {
            self::$validateConfig = Yii::$app->params['validate'];
        }
    }*/

    public function errs()
    {
        return $this->errs;
    }

    public function getTool($params, $route = "")
    {
        self::$regexTool = new ValidateFunctions();
        if (isset(Yii::$app->params['validates'])) {
            self::$validateConfig = Yii::$app->params['validates'];
        }

        if (empty(self::$validatorTool)) {
            self::$validatorTool = new self();
        }
        self::$routeKey  = $route;
        $this->params = $params;

        return self::$validatorTool;
    }

    /**
     * 获取接口路由验证规则
     * @return array
     */
    private function _getValidateRule()
    {
        $route = self::$routeKey;
        if (empty(self::$validateConfig)) {
            return [];
        }
        if (!isset(self::$validateConfig["$route"]) || empty(self::$validateConfig["$route"])) {
            return [];
        }

        // 验证规则
        return self::$validateConfig["$route"];
    }

    public function validate($data)
    {
        // 验证规则
        $validatorsForAttributes = $this->_getValidateRule();

        foreach ($validatorsForAttributes as $k => $v) {//循环每一个验证字段
            if (empty($v)) {
                continue;
            }

            $regulars       = $v[0];
            $field          = isset($data["$k"]) ? $data["$k"] : "";//验证数据
            $logic          = isset($v[2]) ? strtolower($v[2]) : 'and';
            $errdata        = array();
            $hasForOneField = false;

            foreach ($regulars as $index => $regular) {//每一个字段中的每一个规则
                $errMsg   = Yii::t('app', $regular, $k);
                $regulars = explode(" ", $regular);
                if (!$field && $regular == "notEmpty") {
                    $this->errs[] = isset($v[1][$index]) && !empty($v[1][$index]) ? $v[1][$index] : $errMsg;
                }

                if (isset($regulars[1]) && ($regular = $regulars[0])) {
                    unset($regulars[0]);
                    // 规则后面跟多个限定值
                    if (strpos(implode(",", $regulars), ",") !== false) {
                        $validateResult = self::$regexTool->$regular($field, array_values($regulars));
                    }
                    // 规则后面只有一个限定值
                    else {
                        $validateResult = self::$regexTool->$regular($field, $regulars[1]);
                    }
                    // 校验不通过
                    if (!$validateResult) {
                        $param = '[' . implode(',', $regulars) . ']';
                        $regulars[0] = $k;
                        //验证range时构造提示信息参数
                        if ($regular == 'range') {
                            $regulars_param = [$k, $param];
                            $errMsg      = Yii::t('app', $regular, $regulars_param);
                        } else {
                            $errMsg      = Yii::t('app', $regular, $regulars);
                        }
                        $errdata[]   = isset($v[1][$index]) && !empty($v[1][$index]) ? $v[1][$index] : $errMsg;
                    }
                } elseif (!isset($regulars[1]) && $field && !self::$regexTool->$regular($field)) {
                    $errdata[] = isset($v[1][$index]) && !empty($v[1][$index]) ? $v[1][$index] : $errMsg;
                } elseif ($logic == "or") {
                    $hasForOneField = true; // or条件下满足一个验证即可
                    continue;
                }
            }

            if ($logic == "or" && $hasForOneField) {
                unset($errdata);
            }

            if (!empty($errdata)) {
                $this->errs = array_merge($this->errs, $errdata);
            }
        }
        $validateResult = $this->errs ? false : true;

        return $validateResult;
    }

    /**
     * 根据路由验证参数
     * @param $data 参数
     */
    public function validateRoute($data)
    {
        $route         = yii::$app->requestedRoute;
        $validatorTool = $this->getTool($data, $route);
        // 参数过滤
        $data = $validatorTool->_filterParams($data);
        // 参数校验
        $result = $validatorTool->validate($data);
        if (!$result) {
            $msg = trim(implode(", ", $validatorTool->errs()), ',');

            if ($msg) {
                $result = Yii::$app->rest->output(1001, null, $msg);
            } else {
                $result = Yii::$app->rest->output(0);
            }

            $response         = Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->data   = $result;

            Yii::$app->response->send();
            exit;
        }
    }

    /**
     * 服务方法数据验证
     *
     * @param $data 数据
     * @param $class
     * @param $method
     * @param $module
     * @return string
     */
    public function validateService($data, $class, $method, $module = null)
    {
        $this->getModuleConfigByModuleId($module);
        $route         = $class . ':' . $method;
        $validatorTool = $this->getTool($data, $route);
        // 参数过滤
        $data = $validatorTool->_filterParams($data);
        // 参数校验
        $result = $validatorTool->validate($data);
        if (!$result) {
            $msg = trim(implode(", ", $validatorTool->errs()), ',');

            if ($msg) {
                $result = Yii::$app->rest->output(1001, null, $msg);
            } else {
                $result = Yii::$app->rest->output(0);
            }

            return $result;
        } else {
            return '';
        }
    }

    /**
     * 参数过滤
     * @param $data
     * @return mixed
     */
    private function _filterParams($data)
    {
        $rules = $this->_getValidateRule();
        foreach ($data as $k => $v) {
            $filterFlag = true;
            if (isset($rules[$k]) && isset($rules[$k][3])) {
                $filterFlag = $rules[$k][3];
            }
            if ($filterFlag) {
                $data[$k] = $this->_filterData($data["$k"]);
            }
        }
        // 参数过滤
        if (yii::$app->request->isPost || yii::$app->request->isPut) {
            yii::$app->request->setBodyParams($data);
        } else {
            yii::$app->request->setQueryParams($data);
        }

        return $data;
    }

    /**
     * 过滤数据
     *
     * @param $data
     * @return array|string
     */
    private function _filterData($data)
    {
        if (is_string($data)) {
            $data = HtmlPurifier::process($data);

            return strip_tags($data);
        } elseif (is_array($data)) {
            $ret = [];
            foreach ($data as $k => $v) {
                $ret[$k] = $this->_filterData($v);
            }

            return $ret;
        }
        return $data;
    }

    /**
     * 通过moduleId获取需要的配置文件
     * @param $moduleId
     */
    private function getModuleConfigByModuleId($moduleId)
    {
        $pathConfigs = dirname(__DIR__) . "/../modules/{$moduleId}/configs/validates.php";

        if (file_exists($pathConfigs)) {
            $moduleConfigs    = ['validates' => require($pathConfigs)];
            Yii::$app->params = array_merge(Yii::$app->params, $moduleConfigs);
        }
    }
}

?>