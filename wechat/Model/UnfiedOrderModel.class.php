<?php
/**
 * 统一下单接口类
 */
namespace Model;
use Vendor\Fundation\Config;

class UnifiedOrderModel extends RequestModel {
    public function __construct() {
        //设置接口链接
        $this->url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
        //设置curl超时时间
        $this->curl_timeout = Config::get('wechat', 'CurlTimeout');
    }

}