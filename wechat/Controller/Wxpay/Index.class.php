<?php
namespace Controller\Wxpay;
use Controller\BaseController;
use \Model\Wxpay;

class Index extends BaseController {
    public function response() {
        $model = new Wxpay();
        $this->assign('wxPayUrl', $model->wxPayUrl());
        $this->display('index');
    }
}