<?php
ini_set('display_errors', on);
error_reporting(E_ALL);
class Test {
    public static function autoloadClass($className) {
        echo $className;
    }

    public static function getLoader() {
        spl_autoload_register(['Test', 'autoloadClass'], true, true);
        new \Composer\Haha();
    }
}
Test::getLoader();