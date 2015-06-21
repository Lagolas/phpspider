<?php
namespace Common\Controller;
use Think\Controller;
class InitController extends Controller{
    public function __construct() {
        parent::__construct();
        $this->init();
    }
    private function init(){
        define('SOURCE_PATH', COMMON_PATH.'View/statics/');//静态资源目录
        define('PUBLIC_PATH', "/Public/");
        define('UPLOAD_ROOT_PATH','./Public/');
    }
}