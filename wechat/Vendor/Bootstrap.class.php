<?php
namespace Vendor;

use Vendor\Fundation\Config;

class Bootstrap {
    /**
     * 初始化config
     */
    public static function initConfig() {
        //注册基本配置
        Config::register('wechat', WECHAT_DIR. '/Config/wechat.config.php');
    }

    public static function getController() {
        //path
        $path = parse_url(ltrim($_SERVER['REQUEST_URI'], '/'), PHP_URL_PATH);

        //分解path
        $pathSegment = explode('/', $path);
        if (count($pathSegment) !== 2) {
            return null;
        }

        //处理controlelr相关信息
        $ctrlDir = ucfirst($pathSegment[0]);
        $ctrlClassName = implode('', array_map(function($childSeg){
            return ucfirst($childSeg);
        },explode('_', $pathSegment[1])));
        if (!ctype_alnum($ctrlDir) || !ctype_alnum($ctrlClassName)) {
            return null;
        }

        //加载ctroller
        $controller = 'Controller\\'.$ctrlDir.'\\'.$ctrlClassName;
        return new $controller;
    }
}