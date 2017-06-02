<?php

namespace app\packages\Filter;

use yii\filters\auth\AuthMethod;
use yii;

/**
 * QueryParamAuth is an action filter that supports the authentication based on the access token passed through a query parameter.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class HttpTokenAuth extends AuthMethod
{
    /**
     * @var string the parameter name for passing the access token
     */
    public $tokenParam = 'access-token';


    /**
     * 从http请求头中获取access-token进行认证
     * @inheritdoc
     */
    public function authenticate($user, $request, $response)
    {
        $accessToken = yii::$app->request->headers->get('access-token');
        if (is_string($accessToken)) {
            $identity = $user->loginByAccessToken($accessToken, get_class($this));
            if ($identity !== null) {
                return $identity;
            }
        }
        if ($accessToken !== null) {
            $this->handleFailure($response);
        }

        return null;
    }
}
