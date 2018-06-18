<?php
namespace Model;
use Vendor\Fundation\Config;

class Wxpay {
    private $code;
    private $openid;

    public function wxPayUrl() {
        //重定向url
        $orderId = 1;
        $redirectUrl = "http://wechat.cmdapps.com/wxpay/confirm?orderid=1&showwxpaytitle=1";
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

    /**
     * 微信jsapi点击支付
     * @param  [type] $payData [description]
     * @return [type]       [description]
     */
    public function wxPayJsApi($payData) {
        //获取code码，用以获取openid
        $this->code = $_GET['code'];

        //通过code获取openid
        $openid = $this->getOpenId();

        $unifiedOrderResult = null;
        if ($openid != null) {
            //取得统一下单接口返回的数据
            $unifiedOrderResult = $this->getResult($payData, 'JSAPI', $openid);
            //获取订单接口状态
            $returnMessage = $this->returnMessage($unifiedOrder, 'prepay_id');
            if ($returnMessage['resultCode']) {
                $jsApi->setPrepayId($retuenMessage['resultField']);
                //取得wxjsapi接口所需要的数据
                $returnMessage['resultData'] = $jsApi->getParams();
            }

            return $returnMessage;
        }
    }

    /**
     * 通过curl 向微信提交code 用以获取openid
     * @return [type] [description]
     */
    public function getOpenId() {
        //创建openid 的链接
        $url = $this->createOauthUrlForOpenid();
        //初始化
        $ch = curl_init();
        curl_setopt($ch, CURL_TIMEOUT, $this->curl_timeout);
        curl_setopt($ch, CURL_URL, $url);
        curl_setopt($ch, CURL_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURL_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURL_HEADER, FALSE);
        curl_setopt($ch, CURL_RETURNTRANSFER, TRUE);
        //执行curl
        $res = curl_exec($ch);
        curl_close($ch);
        //取出openid
        $data = json_decode($res);
        if (isset($data['openid'])) {
            $this->openid = $data['openid'];
        } else {
            return null;
        }

        return $this->openid;

    }

    /**
     * 生成可以获取openid 的URL
     * @return [type] [description]
     */
    public function createOauthUrlForOpenid() {
        $urlParams['appid'] = Config::get('wechat', 'WxAppid');
        $urlParams['secret'] = Config::get('wechat', 'WxSecret');
        $urlParams['code'] = $this->code;
        $urlParams['grant_type'] = "authorization_code";
        $queryString = $this->ToUrlParams($urlParams, false);
        return "https://api.weixin.qq.com/sns/oauth2/access_token?".$queryString;
    }

    /**
     * 返回统一下单接口结果 （参考https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=9_1）
     * @param  [type] $payData    [description]
     * @param  [type] $trade_type [description]
     * @param  [type] $openid     [description]
     * @return [type]             [description]
     */
    public function getResult($payData, $trade_type, $openid = null) {
        $unifiedOrder = new UnifiedOrder_handle();

        if ($opneid != null) {
            $unifiedOrder->setParam('openid', $openid);
        }
        $unifiedOrder->setParam('body', $payData['body']);  //商品描述
        $unifiedOrder->setParam('out_trade_no', $payData['out_trade_no']); //商户订单号
        $unifiedOrder->setParam('total_fee', $payData['total_fee']);    //总金额
        $unifiedOrder->setParam('attach', $payData['attach']);  //附加数据
        $unifiedOrder->setParam('notify_url', base_url('/Wxpay/pay_callback'));//通知地址
        $unifiedOrder->setParam('trade_type', $trade_type); //交易类型

        //非必填参数，商户可根据实际情况选填
        //$unifiedOrder->setParam("sub_mch_id","XXXX");//子商户号
        //$unifiedOrder->setParam("device_info","XXXX");//设备号
        //$unifiedOrder->setParam("time_start","XXXX");//交易起始时间
        //$unifiedOrder->setParam("time_expire","XXXX");//交易结束时间
        //$unifiedOrder->setParam("goods_tag","XXXX");//商品标记
        //$unifiedOrder->setParam("product_id","XXXX");//商品ID

        return $unifiedOrder->getResult();
    }

    /**
     * 返回微信订单状态
     */
    public function returnMessage($unifiedOrderResult,$field){
        $arrMessage=array("resultCode"=>0,"resultType"=>"获取错误","resultMsg"=>"该字段为空");
        if($unifiedOrderResult==null){
            $arrMessage["resultType"]="未获取权限";
            $arrMessage["resultMsg"]="请重新打开页面";
        }elseif ($unifiedOrderResult["return_code"] == "FAIL")
        {
            $arrMessage["resultType"]="网络错误";
            $arrMessage["resultMsg"]=$unifiedOrderResult['return_msg'];
        }
        elseif($unifiedOrderResult["result_code"] == "FAIL")
        {
            $arrMessage["resultType"]="订单错误";
            $arrMessage["resultMsg"]=$unifiedOrderResult['err_code_des'];
        }
        elseif($unifiedOrderResult[$field] != NULL)
        {
            $arrMessage["resultCode"]=1;
            $arrMessage["resultType"]="生成订单";
            $arrMessage["resultMsg"]="OK";
            $arrMessage["resultField"] = $unifiedOrderResult[$field];
        }
        return $arrMessage;
    }
}