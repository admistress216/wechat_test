<?php
/**
 * 请求型接口基类
 */
namespace Model;
use Vendor\Fundation\Config;

class RequestModel extends BaseModel {
    public $params; //请求参数，类型为关联数组
    public $response; //微信返回的响应
    public $result; //返回参数，类型类关联数组
    public $url; //接口链接
    public $curl_timeout; //curl超时时间

    /**
     * 设置请求参数
     * @param $param
     * @param $paramValue
     */
    public function setParam($param, $paramValue) {
        $this->params[$this->trimString($param)] = $this->trimString($paramValue);
    }

    /**
     * 获取结果,默认不适用证书
     * @return array|mixed|object
     */
    public function getResult() {
        $this->postxml();
        $this->result = $this->xmlToArray($this->response);

        return $this->result;
    }

    /**
     * post请求xml
     * @return bool|mixed
     */
    public function postxml() {
        $xml = $this->createXml();
        $this->response = $this->postXmlCurl($xml, $this->url, $this->curl_timeout);

        return $this->response;
    }

    public function createXml() {
        $this->params['appid'] = Config::get('wechat', 'WxAppid');
        $this->params['mch_id'] = Config::get('wechat', 'Mchid');
        $this->params['nonce_str'] = $this->createNoncestr();
        $this->params['sign'] = $this->getSign($this->params);

        return $this->arrayToXml($this->params);
    }
}