<?php
/**
 * 所有接口基类
 */
namespace Model;
use Vendor\Fundation\Config;

class BaseModel {
    /**
     * 空字符过滤
     *
     * @param $value
     * @return null|string
     */
    public function trimString($value) {
        $ret = null;
        if (null != $value) {
            $ret = trim($value);
            if (strlen($ret) == 0) {
                $ret = null;
            }
        }
        return $ret;
    }

    /**
     * 产生随机字符串
     *
     * @param int $len
     * @return string
     */
    public function createNoncestr($len = 32) {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = '';
        for ($i=0; $i < $len; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * 字符串拼接,签名需要
     *
     * @param $urlParams
     * @param $needUrlencode
     * @return string
     */
    public function ToUrlParams($urlParams, $needUrlencode) {
        $buff = '';
        ksort($urlParams);

        foreach ($urlParams as $k => $v) {
            if ($needUrlencode) $v = urlencode($v);

            $buff .= $k .'='. $v.'&';

        }
        return  strlen($buff) > 0 ? rtrim($buff, '&') : '';
    }

    /**
     * 生成签名
     *
     * @param $obj
     * @return string
     */
    public function getSign($obj) {
        foreach ($obj as $k => $v) {
            $params[$k] = $v;
        }
        //签名步骤一：按字典序排序参数
        ksort($params);
        $str = $this->ToUrlParams($params, false);
        //签名步骤二：在$str后加入key
        $str = $str."&key=".Config::get('Key');
        //签名步骤三：md5加密
        $str = md5($str);
        //签名步骤四：所有字符转为大写
        $result = strtoupper($str);

        return $result;
    }

    /**
     * array转xml
     *
     * @param $arr
     * @return string
     */
    public function arrayToXml($arr) {
        $xml = "<xml>";
        foreach ($arr as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<".$key.">".$val."</".$key.">";
            } else {
                $xml .= "<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }

    /**
     * 将xml转为array
     *
     * @param $xml
     * @return array|mixed|object
     */
    public function xmlToArray($xml) {
        $arr = json_decode(json_encode(simplexml_load_string($xml, 'SinpleXMLElement', LIBXML_NOCDATA)), true);

        return $arr;
    }

    /**
     * 以post方式提交xml到对应的接口
     * @param  [type]  $xml    [description]
     * @param  [type]  $url    [description]
     * @param  integer $second [description]
     * @return [type]          [description]
     */
    public function postXmlCurl($xml, $url, $second = 30) {
        //初始化curl
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch, CURLOPT_URL, $url);
        //这里设置代理，如果有的话
        //curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
        //curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //以post方式提交
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        //执行curl
        $res = curl_exec($ch);

        if ($res) {
            curl_close($ch);
            return $res;
        } else {
            $error = curl_errno($ch);
            echo "curl出错，错误码:$error"."<br>";
            echo "<a href='http://curl.haxx.se/libcurl/c/libcurl-errors.html'>错误原因查询</a></br>";
            curl_close($ch);
            return false;
        }
    }

}