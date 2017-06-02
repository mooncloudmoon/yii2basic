<?php
//输入参数校验
return [
    //控制器验证规则
    'test_v1/rest-test' => [
        'obj_username' => [['notEmpty']],
    ],

    /********************************************************************************************************************/

    //服务类方法验证规则
    'app\modules\test\services\foundations\FoundationService:testFoundation' => [
        "param1"    => [["notEmpty"]],
    ],
];