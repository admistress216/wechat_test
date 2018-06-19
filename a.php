<?php
/**
 * 格式化参数 拼接字符串，签名过程需要使用
 * @param [type] $urlParams     [description]
 * @param [type] $needUrlencode [description]
 */
function ToUrlParams($urlParams, $needUrlencode) {
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
$array = [
    'test1' => '111',
    'test2' => '222'
];
echo ToUrlParams($array,false);