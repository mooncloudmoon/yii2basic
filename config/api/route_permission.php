
<?php
/**
 * 配置路由等级
 * 不同级别APP能访问的路由不一样
 * 空，表示可以访问全部，否则只允许访问许可列表内的
 *
 * allowed_route 是可以自由访问的接口地址，也受IP限制，具体配置 api_iplist:ip_limit_list
 * 路由地址，请按字母顺序升序排列
 * public 开放权限的接口，不登录可以访问
 * limit 需要校验用户是否登录的接口，涉及参数auth_username，auth_token
 * @todo 限制站点请求范围
 */
return [
    //默认
    'client'  => [
        'public' => [
            'visual_v1/user-backend-info',
            'visual_v1/publish',
            'visual_v1/module',
            'visual_v1/case-ids',
            'visual_v1/good-ids',
            'visual_vl/goods',
            'visual_v1/header',
            'visual_v1/user-home-info',
            'visual_v1/pre-home-info',
            'visual_v1/pre-fronted-common',
            'visual_v1/fronted-common',
            'user_v1/user-info',
            'tags_v1/get-tag-all',
            'tags_v1/save-case-tag',
            'tags_v1/get-case-tag',
            'cases_v1/get-cases-thumbnails',

            'file_v1/get-vcs-token-new',//获取vcs token (校验文件后缀是否支持)

        ],
        'limit'  => [
        ]
    ],
    // 主站
    'www'     => [
        'public' => [
            'cases_v1/num'
        ],
        'limit'  => [
        ]
    ],
];