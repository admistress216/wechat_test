<?php
ini_set('display_errors', 'on');
date_default_timezone_set('Asia/Shanghai');
define('WECHAT_DIR', __DIR__);

spl_autoload_register(function($class){
    $filePath = WECHAT_DIR . '/' . str_replace('\\', '/', $class) . '.class.php';
    if (!file_exists($filePath)) {
        die($class . 'not found');
    }
    require $filePath;
});

//加载配置文件
$config = \Vendor\Bootstrap::initConfig();

//针对请求的uri获取对应的controller进行处理
if ($controller = Vendor\Bootstrap::getController()) {
    $controller->response();
} else {

}