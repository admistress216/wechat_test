<?php
class WeChat {
    static $appid;
    static $secret;

    public function __construct($appid, $secret)
    {
        static::$appid = $appid;
        static::$secret = $secret;
    }

    //统一下单接口
    public function makeOrder() {
        $url = "https://api.mch.weixin.qq.com/pay/unifiedorder";

    }


    public function createMenu() {
        $menu = [
            'button' => [
                [
                    'type' => 'view',
                    'name' => '公司官网',
                    'url' => 'http://m.cmdapps.com',
                ],
                [
                    'type' => 'view',
                    'name' => '经典案例',
                    'url' => 'http://m.cmdapps.com/Case.html',
                ],
                [
                    'name' => '关于梦迪',
                    'sub_button' => [
                        [
                            'type' => 'view',
                            'name' => '关于我们',
                            'url' => 'http://m.cmdapps.com/About.html',
                        ],
                        [
                            'type' => 'view',
                            'name' => '联系我们',
                            'url' => 'http://m.cmdapps.com/Contact.html',
                        ],
                        [
                            'type' => 'view',
                            'name' => '加入我们',
                            'url' => 'http://job.cmdapps.com',
                        ],
                    ],
                ]
            ],
        ];
        $menu = json_encode($menu, JSON_UNESCAPED_UNICODE);
        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=". $this->getAccessTocken();
        $code = $this->handlerReq($url, false, $menu)['errcode'];
        return $code === 0 ? 'success!' : 'fail';
    }

    public function getAccessTocken() {
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=". static::$appid ."&secret=". static::$secret;
        $info = $this->handlerReq($url);
        var_dump($info);
        return $info['access_token'];
    }

    /**
     * @param $url
     * @param bool $header array('Content-type: text/plain', 'Content-length: 100')
     * @param string $data
     */
    public function handlerReq($url, $header = false, $data = '') {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url); //访问地址
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //以文件流的形式返回,而不是直接输出

        if ($header) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header); //设置header头
        }

        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POST, true); //post请求
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        $data = curl_exec($ch);
        return json_decode($data, true);
    }
}
$wechat = new Wechat('wx86b351d80e2db287', '3bce215b76ca47f5f0e8f6f6dfb7cf7e');
echo $wechat->createMenu();