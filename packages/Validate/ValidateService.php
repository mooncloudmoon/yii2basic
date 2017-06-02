<?php
namespace app\packages\Validate;

use Exception;

trait ValidateService
{
    public $module = '';

    function validateService($data, $class, $mehtod)
    {
        if ($val = (new ValidatorTools())->validateService($data, $class, $mehtod, $this->module)) {
            throw new Exception($val['message'], $val['ret']);
        }
    }
}