<?php
namespace app\modules\test\services\dependencies;

class DependencyService
{
    private $str = '';

    public function __construct($param)
    {
        $this->str = $param;
    }

    public function testDependency()
    {
        return $this->str;
    }
}