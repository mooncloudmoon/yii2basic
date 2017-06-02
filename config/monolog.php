<?php

return [
    'class' => 'Mero\Monolog\MonologComponent',
    'channels' => [
        //monolog默认配置，不要删除，否则会报错。使用monolog组件时请根据需要选择userLog或者systemLog。
        'main' => [
            'handler' => [
                new \Monolog\Handler\RotatingFileHandler(
                    __DIR__.'/../runtime/logs/system.log',
                    \Monolog\Logger::DEBUG
                )
            ],
            'processor' => [],
        ],

        //用户日志配置
        'userLog' => [
            'handler' => [
                [
                    'type' => 'rotating_file',
                    'path' => __DIR__.'/../runtime/logs/userLog.log',
                    'level' => YII_DEBUG ? \Monolog\Logger::DEBUG : \Monolog\Logger::INFO,
                    'formatter' => new \Monolog\Formatter\LineFormatter("[%datetime%] %channel%.%level_name%: %extra% %message% %context%\n", "Y-m-d H:i:s:u"),
                ],
            ],
            'processor' => [new \Packages\Log\VsoWebProcessor(),new \Packages\Log\VsoIntrospectionProcessor()],
        ],

        //系统日志配置
        'systemLog' => [
            'handler' => [
                [
                    'type' => 'rotating_file',
                    'path' => __DIR__.'/../runtime/logs/systemLog.log',
                    'level' => YII_DEBUG ? \Monolog\Logger::DEBUG : \Monolog\Logger::INFO,
                    'formatter' => new \Monolog\Formatter\LineFormatter("[%datetime%] %channel%.%level_name%: %extra% %message% %context%\n", "Y-m-d H:i:s:u"),
                ],
            ],
            'processor' => [new \Packages\Log\VsoWebProcessor(),new \Packages\Log\VsoIntrospectionProcessor()],
        ],
    ],
];