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
        $this->targetDB = 'mysql://root:admin@localhost:3306/pctest';
        $this->targetPre = "pc_";
        $this->targetNews = "news";
        $this->targetNewsData = "news_data";
        $this->targetHost = "http://pctest.cc";
        $this->targetSiteName = "<a href='http://guaguayule.com'>瓜瓜娱乐</a>";
        $this->targetSiteNameNoa = "瓜瓜娱乐";
    }
}