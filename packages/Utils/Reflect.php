<?php

namespace app\packages\Utils;

class Reflect
{
    /**
     * 获取可以调用的私有方法
     *
     * @param $className
     * @param $methodName
     * @return \ReflectionMethod
     */
    public static function getPrivate($className, $methodName)
    {
        $reflectClass = new \ReflectionClass($className);
        $method       = $reflectClass->getMethod($methodName);
        $method->setAccessible(true);

        return $method;
    }
}
