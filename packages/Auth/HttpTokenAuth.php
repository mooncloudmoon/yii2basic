<?php

namespace app\packages\Auth;

use yii\filters\auth\AuthMethod;
use yii;

class HttpTokenAuth extends AuthMethod
{
    /**
     * @var string the parameter name for passing the access token
     */
    public $tokenParam = 'auth_token';


    /**
     * @inheritdoc
     */
    public function authenticate($user, $request, $response)
    {
        $accessToken = $request->get($this->tokenParam);
        if ($request->isPost || $request->isPut) {
            $accessToken = $request->post($this->tokenParam);
        }
        if (is_string($accessToken)) {
            $identity = $user->loginByAccessToken($accessToken, get_class($this));
            if ($identity !== null) {
                $user->switchIdentity($identity);
            } else {
                $this->handleFailure($response);
            }
            return $identity;
        }
        if ($accessToken !== null) {
            $this->handleFailure($response);
        }

        return null;
    }
}