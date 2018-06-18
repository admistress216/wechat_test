<?php
date_default_timezone_set('PRC');

/**
 * ip白名单
 * deadline为0时为不限制时限
 * @return array['ip' => deadline]
 */
$whiteIpsArr = [
    '123.151.77.70' => '2018-07-21 00:00:00',
    '119.23.239.237' => '2018-07-21 00:00:00',
];

//判断是否是https
function isHttps()
{
    if (!isset($_SERVER['HTTPS'])) return false;

    if ($_SERVER['HTTPS'] === 1 || $_SERVER['HTTPS'] === 'on' || $_SERVER['SERVER_PORT'] === 443) return true;

    return false;
}

function getDomain()
{
    $server_name = $_SERVER['SERVER_NAME'];
    return strpos($server_name, 'www.') !== false ? substr($server_name, 4) : $server_name;
}

$protocol = isHttps() ? 'https' : 'http';
$device = isset($_GET['device']) ? $_GET['device'] : '';
$appid = isset($_GET['appid']) ? $_GET['appid'] : '';
$state = isset($_GET['state']) ? $_GET['state'] : '';
$redirect_uri = isset($_GET['redirect_uri']) ? $_GET['redirect_uri'] : '';
$code = isset($_GET['code']) ? $_GET['code'] : '';
$scope = isset($_GET['scope']) ? $_GET['scope'] : 'snsapi_userinfo';

if (empty($code)) {
//    $deadline = isset($whiteIpsArr[getenv('REMOTE_ADDR')]) ? $whiteIpsArr[getenv('REMOTE_ADDR')] : '';
//    if ($deadline === '') {
//        exit('your ip is not allowed! ip:'.getenv('REMOTE_ADDR'));
//    }
//    if ($deadline < date('Y-m-d')) {
//        exit('expire!');
//    }
    $postfix = $device === 'pc' ? 'qrconnect' : 'oauth2/authorize';
    $authUrl = 'https://open.weixin.qq.com/connect/'. $postfix;

    $options = [
        'appid'         => $appid,
        'redirect_uri'  => $protocol. '://'. $_SERVER['HTTP_HOST']. $_SERVER['SCRIPT_NAME']. "?rui=". urlencode($redirect_uri),
        'response_type' => 'code',
        'scope'         => $scope,
        'state'         => $state
    ];
    //请求wechat
    header('Location: '. $authUrl. "?". http_build_query($options). '#wechat_redirect');
} else {
    $back_url = isset($_GET['rui']) ? urldecode($_GET['rui']) : '';
    if (!empty($back_url)) {
        header('Location: ' . implode('', [
                $back_url,
                strpos($back_url, '?') ? '&' : '?',
                'code=' . $code,
                '&state=' . $state
            ]));
    }
}