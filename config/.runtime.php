<?php

if (!function_exists('yiiParams')) {
    /**
     * 获取yii配置参数
     * @param $key string 对象的属性，或者数组的键值/索引，以'.'链接或者放入一个数组
     * @return mixed mix
     **/
    function yiiParams($key)
    {
        return Yii::$app->params[$key];
    }
}

//增加request id记录
if (empty($_SERVER['HTTP_X_REQUEST_ID'])) {
    $_SERVER['HTTP_X_REQUEST_ID'] = \Ramsey\Uuid\Uuid::uuid1()->toString();
}

//日志相关
if (!function_exists('yBaseLog')) {
    function yBaseLog()
    {
        $logger = yii::$app->monolog->getLogger("userLog");

        return $logger;
    }
}

if (!function_exists('yDebug')) {
    /**
     * monolog记录用户自定义debug日志
     * @param $msg
     */
    function yDebug($msg)
    {
        $logger = yBaseLog();
        $logger->log("debug", $msg);
    }
}

if (!function_exists('yInfo')) {
    /**
     * monolog记录用户自定义info日志
     * @param $msg
     */
    function yInfo($msg)
    {
        $logger = yBaseLog();
        $logger->log("info", $msg);
    }
}

if (!function_exists('yWarning')) {
    /**
     * monolog记录用户自定义warning日志
     * @param $msg
     */
    function yWarning($msg)
    {
        $logger = yBaseLog();
        $logger->log("warning", $msg);
    }
}

if (!function_exists('yError')) {
    /**
     * monolog记录用户自定义error日志
     * @param $msg
     */
    function yError($msg)
    {
        $logger = yBaseLog();
        $logger->log("error", $msg);
    }
}

if (!function_exists('yEmergency')) {
    /**
     * monolog记录用户自定义emergency日志
     * @param $msg
     */
    function yEmergency($msg)
    {
        $logger = yBaseLog();
        $logger->log("emergency", $msg);
    }
}
