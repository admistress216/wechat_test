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
        //取得支付所需要的订单数据
        $orderData = $this->returnOrderData(1);

        //取得jsApi所需要的数据
        $model = new Wxpay();
        $wxJsApiData = $model->wxPayJsApi($orderData);
        //将数据分配到模板去，在js里使用
        $this->assign('wxJsApiData', json_encode($wxJsApiData, JSON_UNESCAPED_UNICODE));
        $this->assign('order', $orderData);
        $this->display('confirm');
    }

    /**
     * 返回支付所需要的数据
     * @param  [type] $orderId 订单号
     * @param  string $data    订单数据，当$data数据存在时刷新$orderData缓存，因为订单号不唯一
     * @return [type]          [description]
     */
    public function returnOrderData($orderId, $data = '') {
        $data = [
            'id' => 1,
            'user_id' => 11111111,
            'sn' => 102130230232,
            'fee' => 1,
            'time' => '2018-01-12 12:00:00',
            'goods_name' => '测试商品',
            'attach' => '附加'
        ];
        //支付前缓存所需要的数据
        $orderData['id'] = $data['id'];

        //支付平台需要的数据
        $orderData['user_id'] = $data['user_id'];
        $orderData['sn'] = $data['sn'];
        //这是唯一编号
        $orderData['order_no'] = substr(md5($data['sn'].$data['fee']), 8, 8).$data['sn'];
        $orderData['fee'] = $data['fee'];
        $orderData['time'] = $data['time'];
        $orderData['goods_name'] = $data['goods_name'];
        $orderData['attach'] = $data['attach'];

        return $orderData;
    }
}