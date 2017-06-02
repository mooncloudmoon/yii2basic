<?php

namespace app\packages\Vcs;

use app\packages\Utils\Str;
use yii;

class Vcs
{
    const OP_VIDEO = "avthumb/360|avthumb/720";
    const SIZE_VIDEO_QINIU = '1280x720';

    const SIZE_VIDEO_BIG = '1280x720';
    const SIZE_VIDEO_SMALL = '640x360';
    const SIZE_VIDEO_ORIGIN = 'origin';

    /**
     * 获取操作媒体中心用的token
     * @param string $key
     * @param string $op
     * @return string $vcsToken
     */
    public static function getVcsToken($key, $op = '')
    {
        $postData = [
            'a'  => yii::$app->params['Vcs']['appid'],
            'b'  => yii::$app->params['Vcs']['bucket'],
            'k'  => $key,
            'op' => $op,
        ];

        $serializeData = Str::urlSafeBase64Encode(json_encode($postData, JSON_UNESCAPED_SLASHES));
        $hashData      = hash_hmac("sha256", $serializeData, yii::$app->params['Vcs']['appkey']);
        $encryption    = Str::urlSafeBase64Encode($hashData);
        $vcsToken      = $encryption . ':' . $serializeData;

        return $vcsToken;
    }

    /**
     * 获取文件下载/显示的URL
     * @param type $b
     * @param type $key
     * @param type $op
     * @return string
     */
    public static function getVcsDownloadUrl($key, $op)
    {
        $vcsToken = self::getVcsToken($key, $op);
        $url      = yii::$app->params['Vcs']['cloud_api']['download'] . yii::$app->params['Vcs']['bucket'];

        $url .= '/' . $key . '?token=' . $vcsToken;

        return $url;
    }

    /**
     * 视频信息
     * @param $key
     * @param string $token
     * @return mixed
     */
    public static function getVideoFileInfo($key, $token = '')
    {
        $op     = self::OP_VIDEO;
        $bucket = yii::$app->params['Vcs']['bucket'];
        if (empty($token)) {
            $token = self::getVcsToken($key, $op);
        }
        $url = yii::$app->request->hostInfo . yii::$app->params['Vcs']['cloud_api']['info'] . "/{$bucket}/{$key}";

        $result = yii::$container->get('Http')->get($url, ['token' => $token]);

        return json_decode($result, true);
    }

    /**
     * 生成key
     * @param string $ext 文件后缀名
     * @param int $len 生成随机字符需要额外的字符个数
     * @return string
     */
    public static function generateKey($ext = '', $len = 32)
    {
        $ext = strtolower($ext);
        return self::createVcsKey($len) . '.' . ltrim($ext, '.');
    }

    /**
     * 为了统一与前台js生成key的规则一致
     * @param  $len int 生成随机字符需要额外的字符个数
     * @return string
     */
    public static function createVcsKey($len = 32)
    {
        //时间戳精确到毫秒
        $timestamp = floor(microtime(true) * 1000);
        //生成的随机字符串
        $randomStr = self::getRandomStr($len);

        return md5($timestamp . $randomStr);
    }

    /**
     * 生成随机数[时间戳（精确到毫秒）+ 指定位数的随机字符]
     * @param  $len int 时间戳外的随机字符个数
     * @return string
     */
    private static function getRandomStr($len = 32)
    {
        //时间戳精确到毫秒
        $timestamp = floor(microtime(true) * 1000);
        //指定的字符
        $str = '0123456789qwertyuioplkjhgfdsazxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM';
        //字符串长度
        $strLen = strlen($str);
        //将字符串转换成数组
        $strArr = str_split($str, 1);
        //产生指定数目的随机字符串
        $tmp = '';
        for ($i = 0; $i < $len; $i++) {
            $pos = ceil(mt_rand(0, 100000000) % $strLen);
            $tmp .= $strArr[$pos];
        }

        //返回最终生成的随机字符串
        return $timestamp . $tmp;
    }
}