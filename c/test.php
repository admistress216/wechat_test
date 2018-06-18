<?php
require_once "lib.php";

//APPID
define("APPID" , "省略");

//APPSECRET
define("APPSECRET" , "省略");

if( isset( $_GET['code'])){

    //获取授权页回调过来的code值
    define("CODE",$_GET['code']);

    //请求access_token的url
    $get_access_token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".APPID."&secret=".APPSECRET."&code=".CODE."&grant_type=authorization_code";
    $json_result = getCach($get_access_token_url);
    $arr_result = json_decode($json_result,true);
    define("ACCESS_TOKEN",$arr_result['access_token']);
    define("OPEN_ID",$arr_result['openid']);

    //获取用户基本信息的接口url
    $get_user_info_url = "https://api.weixin.qq.com/sns/userinfo?access_token=".ACCESS_TOKEN."&openid=".OPEN_ID;
    $userinfo_json = getCach($get_user_info_url);
    $userinfo_arr = json_decode($userinfo_json,true);

    //获得用户的openid
    $openid = $userinfo_arr['openid'];
    //获得用户的昵称
    $nickname = $userinfo_arr['nickname'];
    //获得用户的性别
    $sex = $userinfo_arr['sex'];
    //获得用户的头像url
    $imgicon = $userinfo_arr['headimgurl'];

}
?>