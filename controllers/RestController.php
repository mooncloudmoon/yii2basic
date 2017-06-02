<?php
namespace app\controllers;

use app\packages\Auth\HttpTokenAuth;
use app\packages\Error\RestApiErrorHandler;
use app\packages\Extend\Module\BindModuleService;
use app\packages\Validate\ValidatorTools;
use yii;
use yii\rest\Controller;
use yii\filters\RateLimiter;
use app\packages\CommonRegister\CommonRegister;

/**
 * Class RestController
 * @package app\controllers
 */
class RestController extends Controller
{
    private $_start = 0;
    private $_end = 0;
    private $_app_id = '';
    private $_token = '';
    private $_request_uri = ''; // 请求参数，用于日志
    private $_params = '';      // 请求参数，用于校验

    public $noAuthActionArr = [];   // 无需验证的方法
    public $isServerVisit = false;  // 是否是服务端访问

    /**
     * 请求方法动词限制
     * @return array
     */
    public function verbs()
    {
        $verbs = parent::verbs();

        $verbs['index']  = ['GET'];
        $verbs['view']   = ['GET'];
        $verbs['create'] = ['POST'];
        $verbs['update'] = ['PUT'];
        $verbs['delete'] = ['DELETE'];
        $verbs['list']   = ['GET'];

        return $verbs;
    }

    public function init()
    {
        parent::init();
        $this->setErrorHandler();
    }

    /**
     * 设置错误处理
     * @throws yii\base\InvalidConfigException
     */
    private function setErrorHandler()
    {
        $restApiErrorHandler = new RestApiErrorHandler();
        Yii::$app->set('errorHandler', $restApiErrorHandler);
        $restApiErrorHandler->register();
    }

    public function beforeAction($action)
    {
        // 参数初始化
        $this->paramsInit();

        //username注册
        $this->usernameRegister();

        // 服务端权限校验
        $this->checkSiteToken();

        // 模块依赖处理&配置加载,需要加载用户认证服务
        $this->currentModuleInit();

        // 客户端的校验在这里触发( behaviors() )
        $result = parent::beforeAction($action);

        // 验证通过,记录参数
        $this->addRequestRecord();

        // 请求参数校验
        (new ValidatorTools())->validateRoute($this->_params);

        return $result;
    }

    /**
     * 权限校验，针对用户
     * @return array
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        // 当前操作的id
        $currentAction = Yii::$app->controller->action->id;
        // 需要进行认证的action
        $noAuthActionArr = $this->noAuthActionArr;

        // 需要进行认证的action就要设置安全认证类
        if (!$this->isServerVisit && !in_array($currentAction, $noAuthActionArr)) {
            $this->checkGroupPermission('client');
            $behaviors['authenticator'] = [
                'class' => HttpTokenAuth::className(),
            ];
            //速率限制
//            $behaviors['rateLimiter'] = [
//                'class'                  => RateLimiter::className(),
//                'enableRateLimitHeaders' => true,
//            ];
        }

        return $behaviors;
    }

    /**
     * 模块依赖处理&配置文件加载
     */
    private function currentModuleInit()
    {
        $moduleId = $this->getModuleId();

        if ($moduleId) {
            $this->setModuleId($moduleId);

            BindModuleService::bind();

            $this->getModuleConfigByModuleId($moduleId);
        }
    }

    /**
     * 设置moduleId
     * 因为不走controller,拿不到moduleId
     * 语言包等配置需要,所以在这里设置
     *
     * @param $moduleId
     */
    private function setModuleId($moduleId)
    {
        yii::$app->controller->module->id = $moduleId;
    }

    /**
     * 获取moduleId
     * @return string
     */
    private function getModuleId()
    {
        $className    = Yii::$app->controller->className();
        $classNameArr = explode("\\", $className);

        $moduleId = '';
        if (!empty($classNameArr) && isset($classNameArr[2])) {
            $moduleId = $classNameArr[2];
        }

        return $moduleId;
    }

    /**
     * 通过moduleId获取需要的配置文件
     * @param $moduleId
     */
    private function getModuleConfigByModuleId($moduleId)
    {
        $pathConfig = dirname(__DIR__) . "/modules/{$moduleId}/configs/validates.php";

        if (file_exists($pathConfig)) {
            $moduleConfigs    = ['validates' => require_once($pathConfig)];
            Yii::$app->params = array_merge(Yii::$app->params, $moduleConfigs);
        }
    }

    /**
     * 参数初始化
     */
    private function paramsInit()
    {
        $this->_start = $this->getMillisecond();
        // 请求地址，用于日志记录&问题复现
        $this->_request_uri = $this->route;
        $this->_app_id      = yii::$app->request->get('appid');
        $this->_token       = yii::$app->request->get('token');
        $this->_params      = yii::$app->request->get();

        switch ($_SERVER['REQUEST_METHOD']) {
            case "GET":
            case "DELETE":
            case "HEAD":
                $this->_request_uri = $_SERVER['REQUEST_URI'];
                break;
            case "POST":
            case "PUT":
                $this->_app_id      = yii::$app->request->post('appid');
                $this->_token       = yii::$app->request->post('token');
                $this->_params      = yii::$app->request->post();
                $queryString        = http_build_query(yii::$app->request->post());
                $route              = trim(yii::$app->params['domain_space'], "/") . $_SERVER['REQUEST_URI'] . '"';
                $this->_request_uri = '-d "' . $queryString . '" "' . $route;
                break;
            case "PATCH":
            case "COPY":
            case "OPTIONS":
            case "VIEW":
                break;
        }
    }

    /**
     * 权限校验，针对站点
     * @return mixed
     */
    private function checkSiteToken()
    {
        $appid = $this->_app_id;
        $token = $this->_token;

        if (!empty($appid) && !empty($token)) {
            $configAppIdAndToken = yii::$app->params['appid_token'];

            if (!isset($configAppIdAndToken[$appid])) {
                return yii::$app->rest->output(1008);
            }
            $api_token = $configAppIdAndToken[$appid]['token'];
            $group     = $configAppIdAndToken[$appid]['group'];

            $this->checkGroupPermission($group);

            if ($api_token && $api_token != $token) {
                return yii::$app->rest->output(1008);
            }

            $this->isServerVisit = true;
        }
    }

    private function checkGroupPermission($group)
    {
        //如果有配置路由列表，则进行限制，否则不限制
        $routeArr   = yii::$app->params[$group];
        $routeMerge = array_merge($routeArr['public'], $routeArr['limit']);

        if ($routeMerge) {
            if (!in_array($this->route, $routeMerge)) {
                return yii::$app->rest->output(1018, null, '', [$this->route]);
            }
        }
    }

    private function getMillisecond()
    {
        list($s1, $s2) = explode(' ', microtime());

        return (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
    }

    /**
     * 记录请求的参数
     */
    private function addRequestRecord()
    {
        $data = [
            $this->route,
            $this->_request_uri
        ];
        yInfo(json_encode($data, JSON_UNESCAPED_SLASHES));
    }

    /**
     * 记录消耗的时间
     */
    private function addCostTimeRecord()
    {
        $this->_end = $this->getMillisecond();
        $data       = ['time_cost' => $this->_end - $this->_start];
        yInfo(json_encode($data, JSON_UNESCAPED_SLASHES));
    }

    /**
     *注册登录用户和访问用户
     */
    private function usernameRegister()
    {
        $username     = yii::$app->request->get('auth_username', "");
        $obj_username = yii::$app->request->get('obj_username', "");
        switch ($_SERVER['REQUEST_METHOD']) {
            case "POST":
            case "PUT":
                $username     = yii::$app->request->post('auth_username', "");
                $obj_username = yii::$app->request->post('obj_username', "");
        }

        yInfo('CommonRegister about username, username: ' . $username . ', obj_username: ' . $obj_username);

        CommonRegister::set("username", $username);
        CommonRegister::set("obj_username", $obj_username);
    }

    public function afterAction($action, $result)
    {
        $this->addCostTimeRecord();

        return parent::afterAction($action, $result);
    }
}