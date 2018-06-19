<?php
/**
 * 响应型接口基类
 */
namespace Model;

class ResponseModel extends BaseModel {
    public $data; //接收到的数据,类型为关联数组
    public $returnParams; //返回参数,类型为关联数组

    /**
     * 将微信请求的xml转换为关联数组
     *
     * @param $xml
     */
    public function saveData($xml) {
        $this->data = $this->xmlToArray($xml);
    }

    /**
     * 验证签名
     * @return bool
     */
    public function checkSign() {
        $tmpData = $this->data;
        unset($tmpData['sign']);
        $sign = $this->getSign($tmpData);
        if ($this->data['sign'] == $sign) {
            return true;
        }
        return false;
    }

    /**
     * 设置返回微信的xml数据
     */
    function setReturnParameter($parameter, $parameterValue)
    {
        $this->returnParams[$this->trimString($parameter)] = $this->trimString($parameterValue);
    }
}