<?php
namespace Controller;

class BaseController {
    private $tmpArray = [];

    public function assign($name, $value) {
        $this->tmpArray[$name] = $value;
    }

    public function display($filename) {
        extract($this->tmpArray);
        require WECHAT_DIR.'/View/'.$filename.'.php';
    }
}