<?php
namespace Model;
use Vendor\Fundation\Config;

class Wxpay {
    public function wxPayUrl() {
        //重定向url
        $orderId = 1;
        $redirectUrl = "http://wechat.cmdapps.com/wxpay/confirm/".$orderId."?showwxpaytitle=1";
        $urlParams['appid'] = Config::get('wechat', 'WxAppid');
        $urlParams['redirect_uri'] = $redirectUrl;
        $urlParams['response_type'] = 'code';
        $urlParams['scope'] = 'snsapi_base';
        $urlParams['state'] = "STATE"."#wechat_redirect";
        //拼接字符串
        $queryString = $this->ToUrlParams($urlParams, false);
        return "https://open.weixin.qq.com/connect/oauth2/authorize?".$queryString;
    }

    /**
     * 格式化参数 拼接字符串，签名过程需要使用
     * @param [type] $urlParams     [description]
     * @param [type] $needUrlencode [description]
     */
    public function ToUrlParams($urlParams, $needUrlencode) {
        $buff = "";
        ksort($urlParams);

        foreach ($urlParams as $k => $v) {
            if($needUrlencode) $v = urlencode($v);
            $buff .= $k .'='. $v .'&';
        }

        $reqString = '';
        if (strlen($buff) > 0) {
            $reqString = substr($buff, 0, strlen($buff) - 1);
        }

        return $reqString;
    }
}