<?php
/**
 * 手机端微信支付，此处是授权获取到code时的回调地址
 * @param  [type] $orderId 订单编号id
 * @return [type]          [description]
 */

namespace Controller\Wxpay;
use \Model\Wxpay;

use Controller\BaseController;

class Confirm extends BaseController {
    public function response() {
        //统一下单接口所需数据
        $payData['sn'] = 1;
//        $payData['body'] = $data['goods_name'];
//        $payData['out_trade_no'] = $data['order_no'];
//        $payData['total_fee'] = $data['fee'];
//        $payData['attach'] = $data['attach'];

        //取得jsApi所需要的数据
        $model = new Wxpay();
        $wxJsApiData = $model->wxPayJsApi($payData);
        //将数据分配到模板去，在js里使用
        $this->assign('wxJsApiData', json_encode($wxJsApiData, JSON_UNESCAPED_UNICODE));
        $this->assign('order', $payData);
        $this->display('confirm');
    }
}