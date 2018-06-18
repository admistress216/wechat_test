<?php
/**
 * 封装向微信服务器发送http请求的post和get方法
 *
 */


/**
 * 初始化，根据APPID和APPSECRET获取ACCESS_TOKEN
 *
 */
function init(){

    //设置默认时区
    date_default_timezone_set( "Asia/shanghai");

    //APPID
    define("APPID" , "省略");

    //APPSECRET
    define("APPSECRET" , "省略");

    $token_access_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".APPID."&secret=".APPSECRET;

    //获取access_token请求的url地址
    $res = file_get_contents( $token_access_url );

    //将获取的返回json值转换为数组格式
    $arr_result = json_decode($res,true);

    //定义为全局方便使用
    define("ACCESS_TOKEN" , $arr_result['access_token']);

}


/**
 * post方式提交数据
 *
 * @param $url   提交的url地址
 * @param $data  post需要的数据
 * @return object 返回提交后返回的json对象数据
 */
function postMessage( $url , $data){
    $ch = curl_init();
    curl_setopt($ch , CURLOPT_URL , $url );
    curl_setopt($ch , CURLOPT_CUSTOMREQUEST , "POST");
    curl_setopt($ch , CURLOPT_SSL_VERIFYPEER , FALSE);
    curl_setopt($ch , CURLOPT_SSL_VERIFYHOST , FALSE);
    curl_setopt($ch , CURLOPT_USERAGENT , 'Mozilla/5.0 (compatible; MSIE
                                                 5.01 ; Windows NT 5.0)');
    curl_setopt($ch , CURLOPT_FOLLOWLOCATION , 1 );
    curl_setopt($ch , CURLOPT_AUTOREFERER , 1 );
    curl_setopt($ch , CURLOPT_POSTFIELDS , $data );
    curl_setopt($ch , CURLOPT_RETURNTRANSFER , true );

    $info = curl_exec($ch);                               //将执行返回的数据保存到临时变量
    if( curl_errno($ch) ){                                //判断数据在执行过程中是否有错误
        echo 'Errno'.curl_error($ch);

    }

    curl_close($ch);
    return $info ;
}


/**
 * get方式获取数据 , 通过curl
 *
 * @param $url   提交的url地址
 * @return object 返回获取json对象数据
 */
function getCach($url){

    $ch = curl_init();
    curl_setopt($ch , CURLOPT_URL , $url );
    curl_setopt($ch , CURLOPT_HEADER, false);
    curl_setopt($ch , CURLOPT_RETURNTRANSFER , true );
    curl_setopt($ch , CURLOPT_SSL_VERIFYPEER , false);
    curl_setopt($ch , CURLOPT_USERAGENT , 'Mozilla/5.0 (Windows NT 6.1)
                                           AppleWebKit/537.11 ( KHTML , like Geko ) Chrome/23.0.1271.1 Safari/537.11');
    $res = curl_exec($ch);
    $rescode = curl_getinfo($ch , CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $res;

}

/**
 * get方式获取数据
 *
 * @param $url   提交的url地址
 * @return object 返回读取的数据
 */
function getFileGetContent($url){
    $result = file_get_contents($url);
    return $result ;
}

/**
 * 格式输出调试信息
 *
 */
function p($arr){
    var_dump($arr);
}