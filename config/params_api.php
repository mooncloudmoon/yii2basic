<?php
/**
 * 请求各子站or服务的接口配置
 */

$api_prefix     = 'https://api.vsochina.com/';

return [
    //请求速率限制,单位时间内允许的请求的最大数目，例如，[10, 60] 表示在60秒内最多请求10次。
    'request_rate_limit' => [30, 60],

    // api接口中心
    'Api'     => [
        'app_id'             => 2015,
        'app_token'          => 'i1GReniJCqGs7uK7nQjxqWQ1M2QwZDc0OTRlNmYyYjIxMmE1OTRmNmY3M2NjOGYz',
        'user_login_status'  => $api_prefix . 'user/user/login-status', //用户登录状态接口
        'user_detail'        => $api_prefix . 'user/info/view-space',   //获取个人信息
        'user_auth_status'   => $api_prefix . 'auth/record/get-user-auth-status-list',  //用户认证状态列表
        'user_avatar'        => $api_prefix . 'user/info/view-avatar',  //用户头像查询，支持批量
        'favor_talent'       => $api_prefix . 'favor/talent/create-favor-talent',   //关注用户
        'del_favor_talent'   => $api_prefix . 'favor/talent/delete-favor-talent',   //取消关注用户
        'favor_status'       => $api_prefix . 'favor/talent/state-favored', //A是否已关注B
        'send_message'       => $api_prefix . 'message/message/send',   //发送站内信
        'industry_list'      => $api_prefix . 'industry/list',    // 行业列表
        'available_username' => $api_prefix . 'user/auth/is-available-username',    // 用户名是否可用
    ],
];
