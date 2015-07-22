<?php
namespace Home\Controller;
use Common\Controller\InitController;
class MemberController extends InitController{
    public function __construct() {
        parent::__construct();
    }
    public function login(){
        if(cookie('authcookie')){
            redirect(U('Crawl/index'));
        }
        $this->display();
    }
    public function gologin(){
        if(!IS_POST) exit;
        $where['name'] = I('post.name');
        $db = M('member');
        $userinfo = $db->where($where)->find();
        if(intval($userinfo['state'])!==1) {$this->error ('禁止登陆，请联系管理员');exit;}
        $salt = $userinfo['salt'];
        $subpwd = I('post.password');
        if(MD5($subpwd."+".$salt)==$userinfo['password']){
            cookie('authcookie',  authcode($where['name'].'-'.GetIP().'-'.$userinfo['id'],'ENCODE'));
            $this->success('登陆成功',U('Crawl/index'));
        }else{
            $this->error('用户名或密码错误');
        }
    }
    
    public function logout(){
        cookie('authcookie',null);
        $this->success('已经安全退出',U('member/login'));
    }
}