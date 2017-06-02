<?php
namespace app\packages\Auth;

use yii;
use yii\web\IdentityInterface;
use yii\filters\RateLimitInterface;

//一旦 IdentityModel 实现RateLimitInterface接口， Yii 会自动使用 yii\filters\RateLimiter为 yii\rest\Controller 配置一个行为过滤器来执行速率限制检查
class IdentityModel implements IdentityInterface//, RateLimitInterface
{
    public $id;
    public $authKey;
    public $accessToken;

    private static $users;
    private static $auth_username;

    private $allowance = '';
    private $allowance_updated_at = '';

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        self::$users = yii::$app->params['appid_token'];
        foreach (self::$users as $user) {
            if ($user['id'] === $id) {
                return new static($user);
            }
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        self::$users = $token;

        self::$auth_username = yii::$app->request->get('auth_username');
        if (yii::$app->request->isPost || yii::$app->request->isPut) {
            self::$auth_username = yii::$app->request->post('auth_username');
        }

        /** @var \app\models\User $user */
        $user = yii::$container->get("UserAuth");
        $ret  = $user->loginStatus(self::$auth_username, $token);

        if ($ret) {
            $usersdata['accessToken'] = self::$users;

            return new self();
        } else {
            return null;
        }
    }

    //这个就是我们进行yii\filters\auth\QueryParamAuth调用认证的函数，下面会说到。
    public function loginByAccessToken($accessToken, $type)
    {
        //查询数据库中有没有存在这个token
        return static::findIdentityByAccessToken($accessToken, $type);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    // 返回在单位时间内允许的请求的最大数目，例如，[10, 60] 表示在60秒内最多请求10次。
    public function getRateLimit($request, $action)
    {
        $rateLimit = Yii::$app->params['request_rate_limit'];

        return $rateLimit;
    }

    // 返回剩余的允许的请求数。
    public function loadAllowance($request, $action)
    {
        $allowanceName       = $action->controller->id . '_' . $action->id . '_' . self::$auth_username . '_allowance';
        $allowanceUpdatetime = $action->controller->id . '_' . $action->id . '_' . self::$auth_username . '_allowance_update_at';

        $this->allowance            = Yii::$app->redis->GET($allowanceName);
        $this->allowance_updated_at = Yii::$app->redis->GET($allowanceUpdatetime);

        if (isset($this->allowance) && isset($this->allowance_updated_at)) {
            return [$this->allowance, $this->allowance_updated_at];
        } else {
            $rateLimit = Yii::$app->params['request_rate_limit'];

            return [$rateLimit[0], time()];
        }
    }

    // 保存请求时的UNIX时间戳。
    public function saveAllowance($request, $action, $allowance, $timestamp)
    {
        $allowanceName       = $action->controller->id . '_' . $action->id . '_' . self::$auth_username . '_allowance';
        $allowanceUpdatetime = $action->controller->id . '_' . $action->id . '_' . self::$auth_username . '_allowance_update_at';

        $this->allowance            = $allowance;
        $this->allowance_updated_at = $timestamp;

        Yii::$app->redis->SETEX($allowanceName, ONE_HOUR, $this->allowance);
        Yii::$app->redis->SETEX($allowanceUpdatetime, ONE_HOUR, $this->allowance_updated_at);
    }
}