<?php

namespace app\models;

use Yii;

class User
{
    const LOGIN_STATUS_SUCCESS = 1;

    public function loginStatus($username, $vso_token)
    {
        $params = [
            'appid'     => Yii::$app->params['Api']['app_id'],
            'token'     => Yii::$app->params['Api']['app_token'],
            'username'  => $username,
            'vso_token' => $vso_token
        ];

        $http = Yii::$container->get('Http');
        $ret  = $http->post(Yii::$app->params['Api']['user_login_status'], $params);
        $ret  = json_decode($ret, true);
        if ($ret['status'] == self::LOGIN_STATUS_SUCCESS) {
            return true;
        } else {
            return false;
        }
    }
}
