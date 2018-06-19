<?php
namespace Model;
use Vendor\Fundation\Config;

class Wxpay {
    public function retWxPayUrl() {
        $jsApi = new JsApiModel();
        return $jsApi->createOauthUrlForCode();
    }

    /**
     * 微信jsapi点击支付
     * @param  [type] $payData [description]
     * @return [type]       [description]
     */
    public function wxPayJsApi($data) {
        $jsApi = new JsApiModel();
        //统一下单接口所需数据
        $payData = $this->returnData($data);
        //获取code码，用以获取openid
        $code = $_GET['code'];
        $jsApi->setCode($code);

        //通过code获取openid
        $openid = $jsApi->getOpenId();

        $unifiedOrderResult = null;
        if ($openid != null) {
            //取得统一下单接口返回的数据
            $unifiedOrderResult = $this->getResult($payData, 'JSAPI', $openid);
            //获取订单接口状态
            $returnMessage = $this->returnMessage($unifiedOrderResult, 'prepay_id');
            if ($returnMessage['resultCode']) {
                $jsApi->setPrepayId($returnMessage['resultField']);
                //取得wxjsapi接口所需要的数据
                $returnMessage['resultData'] = $jsApi->getParams();
            }

            return $returnMessage;
        }
    }

    /**
     * 统一下单接口所需要的数据
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function returnData($data) {
        $payData['sn'] = $data['sn'];
        $payData['body'] = $data['goods_name'];
        $payData['out_trade_no'] = $data['order_no'];
        $payData['total_fee'] = $data['fee'];
        $payData['attach'] = $data['attach'];

        return $payData;
    }

    /**
     * 返回统一下单接口结果 （参考https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=9_1）
     * @param  [type] $payData    [description]
     * @param  [type] $trade_type [description]
     * @param  [type] $openid     [description]
     * @return [type]             [description]
     */
    public function getResult($payData, $trade_type, $openid = null) {
        $unifiedOrder = new UnifiedOrderModel();

        if ($openid != null) {
            $unifiedOrder->setParam('openid', $openid);
        }
        $unifiedOrder->setParam('body', $payData['body']);  //商品描述
        $unifiedOrder->setParam('out_trade_no', $payData['out_trade_no']); //商户订单号
        $unifiedOrder->setParam('total_fee', $payData['total_fee']);    //总金额
        $unifiedOrder->setParam('attach', $payData['attach']);  //附加数据
        $unifiedOrder->setParam('notify_url', Config::get('wechat', 'NotifyUrl'));//通知地址
        $unifiedOrder->setParam('trade_type', $trade_type); //交易类型(JSAPI:公众号支付)

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
        $arrMessage = array("resultCode"=>0,"resultType"=>"获取错误","resultMsg"=>"该字段为空");
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

    /**
     * 微信回调接口返回  验证签名并回应微信
     * @param  [type] $xml [description]
     * @return [type]      [description]
     */
    public function wxPayNotify($xml) {
        $notify = new ResponseModel();
        $notify->saveData($xml);
        //验证签名，并回复微信
        //对后台通知交互时，如果微信收到商户的应答不是成功或者超时，微信认为通知失败
        //微信会通过一定的策略（如30分钟共8次），定期重新发起通知
        if ($notify->checkSign() == false) {
            $notify->setReturnParameter("return_code","FAIL");//返回状态码
            $notify->setReturnParameter("return_msg","签名失败");//返回信息
        } else {
            $notify->checkSign=TRUE;
            $notify->setReturnParameter("return_code","SUCCESS");//设置返回码
        }

        return $notify;
    }
}