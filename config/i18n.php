<?php

return [
    'translations' => [
        'app*' => [
            'class'    => 'yii\i18n\PhpMessageSource',
            'basePath' => '@app/i18n',
            'fileMap'  => [
                'app'            => 'i18n_common.php',
                'app/test'      => 'test.php',
            ],
        ],
    ],
];
