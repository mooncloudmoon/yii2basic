<?php
namespace app\packages\Utils;

use yii;

class Str
{
    /**
     * 截取字符串
     * @param $string
     * @param $length
     * @param string $etc
     * @return string
     */
    public static function strCut($string, $length, $etc = '...')
    {
        $result = '';
        $string = html_entity_decode(trim(strip_tags($string)), ENT_QUOTES, 'UTF-8');
        $strLen = strlen($string);
        for ($i = 0; (($i < $strLen) && ($length > 0)); $i++) {
            if ($number = strpos(str_pad(decbin(ord(substr($string, $i, 1))), 8, '0', STR_PAD_LEFT), '0')) {
                if ($length < 1.0) {
                    break;
                }
                $result .= substr($string, $i, $number);
                $length -= 1.0;
                $i += $number - 1;
            } else {
                $result .= substr($string, $i, 1);
                $length -= 0.5;
            }
        }
        $result = htmlspecialchars($result, ENT_QUOTES, 'UTF-8');
        if ($i < $strLen) {
            $result .= $etc;
        }

        return $result;
    }

    /**
     * 从URL中获取主域名
     * @param string $strUrl
     * @return string
     */
    public static function getHostNameByUrl($strUrl)
    {
        return parse_url($strUrl, PHP_URL_HOST);
    }

    /**
     * url safe base64 encode
     * @param type $str
     * @return type
     */
    public static function urlSafeBase64Encode($str)
    {
        return rtrim(strtr(base64_encode($str), '+/', '-_'), '=');
    }

    /**
     * 去除金额小数点后面多余的0
     * @param $s 金额
     * @return mixed|string
     */
    public static function cashNumberFormat($s)
    {
        $s = trim(strval($s));
        if (preg_match('#^-?\d+?\.0+$#', $s)) {
            return preg_replace('#^(-?\d+?)\.0+$#', '$1', $s);
        }
        if (preg_match('#^-?\d+?\.[0-9]+?0+$#', $s)) {
            return preg_replace('#^(-?\d+\.[0-9]+?)0+$#', '$1', $s);
        }

        return $s;
    }

    /**
     * 递归除去字符串两端的空白符
     * @param array $arr 要处理的数组
     * @return array
     */
    public static function trimRecursive($arr) {
        if (empty($arr)) {
            return '';
        }
        if (is_array($arr)) {
            return array_map(__METHOD__, $arr);
        } else {
            return trim($arr);
        }
    }

    /**
     * 校验字符串数量，UTF编码
     * @param $data string
     * @param $min int
     * @param $max int
     * @return bool
     */
    public static function words_num($data = '', $min = 1, $max = 99999999)
    {
        $num = mb_strlen($data, 'utf8');
        if ($num >= $min && $num <= $max)
        {
            return true;
        }
        return false;
    }

    /**
     * 校验html encode的字符串数量，UTF编码
     * @param $data string
     * @param $min int
     * @param $max int
     * @return bool
     */
    public static function str_num($data = '', $min = 1, $max = 99999999)
    {
        $num = mb_strlen(strip_tags(htmlspecialchars_decode($data)), 'utf8');
        if ($num >= $min && $num <= $max)
        {
            return true;
        }
        return false;
    }
}
