<?php
namespace Home\Controller;
use Common\Controller\InitController;
class CrawlController extends InitController{
    public function __construct() {
        parent::__construct();
    }
    
    public function index(){
        $this->display();
    }
    
    public function keyword(){
        $this->display();
    }
    
    public function scheme(){
        $type = I('get.type');
        if(!$type) $this->error ('参数错误');
        $sourcefrom = C('SOURCE_FROM');
        $type = "scheme_".$type;
        $db = M($type);
        $count = $db->count();
        $Page = new \Think\Page($count);
        $schemelist = $db->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('schemelist',$schemelist);
        $this->assign('page',$Page->show());
        $this->assign('sourcefrom',$sourcefrom);
        $this->display($type);
    }
    
    public function url(){
        $this->display();
    }
    
    public function goAddScheme(){
        if(IS_POST){
            $type = I('post.type');
            if($type == 'keyword'){
                $data['keyword'] = I('post.keyword');
                if(!$data['keyword']) $this->error ('关键字不能为空');
                $data['oncenum'] = I('post.oncenum','','intval');
                if($data['artnum']==0) $data['artnum'] = 100;
                $data['sourcefrom'] = I('post.sourcefrom');
                if(!$data['sourcefrom']) $this->error ('请至少选择一个内容来源');
                $data['ctime'] = NOW_TIME;
                $data['downimg'] = I('post.downimg','','intval');
            }elseif($type == 'url'){
                
            }
            $db = M("scheme_".$type);
            if($db->create($data)){
                if($db->add($data)){
                    $this->success('添加成功');
                }
            }else{
                $this->error('添加失败');
            }
        }
    }
    
    public function getListByKeyword(){
        if(IS_GET){
            $scheme = I('get.scheme','','intval');
            if($scheme>0){
                $where['id'] = $scheme;
                $scheme = M('scheme_keyword')->where($where)->find();
                $result = $this->_getList($scheme);
                $history['scheme'] = $scheme['id'];
                $history['num'] = count($result['data']);
                $hid = M('history')->add($history);
                /*
                $where_sort['type'] = 2;
                $where_sort['state'] = 1;
                $category = M('sort')->where($where_sort)->select();
                $this->assign('category',$category);
                 * 
                 */
                $this->assign('hid',$hid);
                $this->assign('result',$result['data']);
                $this->assign('go_on',$result['go_on']);
                $this->assign('schemeid',I('get.scheme','','intval'));
                $this->assign('sourcefrom',$scheme['sourcefrom']);
                $this->display();
            }  
        }
    }
    
    //ajax请求部分
    public function ajaxGetListBykeyword(){
        if(IS_AJAX){
            $scheme = I('get.scheme','','intval');
            $hid = I('get.hid','','intval');
            if($scheme>0 && $hid>0){
                $where['id'] = $scheme;
                $page = I('get.page','','intval');
                $scheme = M('scheme_keyword')->where($where)->find();
                $scheme['hid'] = $hid;
                $result = $this->_getList($scheme,$page);
                $json['state'] = 1;
                $json['result'] = $result['data'];
                $json['go_on'] = $result['go_on'];
                if($result['alertinfo']){
                    $json['alertinfo'] = $result['alertinfo'];
                }
                $json['page'] = $page+1;
                $this->ajaxReturn($json);
            }
        }
    }
    
    //抓取列表
    //@return array()
    private function _getList($scheme,$page=1){
        vendor("HtmlParser/ParserDom");
        $currentfrom = $scheme['sourcefrom'];
        $keyword = urlencode($scheme['keyword']);
        $crawl_num = 0;
        $where['id'] = $scheme['hid'];
        $where['scheme'] = $scheme['id'];
        $history_num = M('history')->where($where)->getField('num');
        $history_num = $history_num>0?$history_num:0;
        $urls = array(
            'ifeng'=>array(
                'selector'=>'.c-title',
                'url'     =>"http://zhannei.baidu.com/cse/search?q=".$keyword."&p=".$page."&s=16378496155419916178",
            ),
            'toutiao'=>array(
                'selector'=>'',
                'url'     =>"http://toutiao.com/search_content/?offset=".(($page-1)*10)."&format=json&keyword=".$keyword."&autoload=true&count=10",
            ),
            'qq'=>array(
                'selector'=>'.vrTitle,.pt',
                'url'     =>"http://www.sogou.com/sogou?site=qq.com&page=".$page."&query=".$keyword,
            ),
            'sina'=>array(
                'selector'=>'h2 a',
                'url'     =>"http://search.sina.com.cn/?q=".$keyword."&range=all&c=news&ie=utf-8&sort=rel&page=".$page,
                'method'  =>'outerHtml'
            ),
            '163'=>array(
                'selector'=>'h3',
                'url'     =>"http://www.yodao.com/search?q=".$keyword."&start=".(($page-1)*10)."&ue=utf8&ttimesort=10&site=tech.163.com",
            ),
            'weixin'=>array(
                'selector'=>'.txt-box h4 a',
                'url'     =>"http://weixin.sogou.com/weixin?query=".$keyword."&type=2&page=".$page."&ie=utf8"
            )
        );
        $data = $this->_curl($urls[$currentfrom]['url']);
        if($currentfrom=='toutiao'){
            $data = json_decode($data);
            $data = $data->data;
            foreach($data as $k=>$v){
                if(stripos($v->display_url,'toutiao.com')){
                    if((intval($history_num)+$crawl_num) >= $scheme['oncenum']){
                        $go_on = 0;
                        break;
                    }
                    $crawl_num++;
                    $single_info['url'] = $v->display_url;
                    $single_info['title'] = strip_tags(trim($v->title));
                    $arr[] = $single_info;
                    $urls_arr[] = md5($v->display_url);
                }
            }
        }else{
            $data = mb_convert_encoding($data, 'utf-8', 'GBK,UTF-8,ASCII');
            $data = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">'.$data;
            $obj = new \ParserDom($data);
            $title_obj = $obj->find($urls[$currentfrom]['selector']);
            if(!$title_obj){
                $where_empty['id'] = $scheme['hid'];
                $history = M('history')->where($where_empty)->find();
                if($history['empty']>=5){
                    $result['go_on'] = 0;
                    if(!$history['num']){
                        $result['alertinfo'] = "采集过于频繁，本次未能获取数据，请稍后再试";
                    }
                }else{
                   M('history')->where($where_empty)->setInc('empty');
                   $result['go_on'] = 1;
                }
                return $result;
            }
            foreach($title_obj as $k=>$v){
                if($currentfrom=='weixin'){
                    $url[0] = $v->getAttr('href');
                }else{
                    $method = $urls[$currentfrom]['method']?$urls[$currentfrom]['method']:"innerHtml";
                    $single = $v->$method();//之所以单独设置方法，是因为有些标题部分会跟随一个【XXX网 xx年xx月xx日】这样的小尾巴，用outerHtml可以直接抓a标签（一般标题在a中，小尾巴不在a中）
                    preg_match('/https?:\/\/[\w\.\/]*\.[\w]?html?/', $single,$url);
                }
                if(!$url)                continue;
                if((intval($history_num)+$crawl_num) >= $scheme['oncenum']){
                    $go_on = 0;
                    break;
                }
                $crawl_num++;
                $single_info['url'] = $url[0];
                $single_info['title'] = trim($v->getPlainText());
                $arr[] = $single_info;
                $urls_arr[] = md5($url[0]);
            }
        }
        $where_in_db['link'] = array('in',$urls_arr);
        $url_in_db = M('history_list')->where($where_in_db)->field('link')->select();
        $i=0;
        foreach($url_in_db as $k=>$v){
            if(in_array($v['link'],$urls_arr)){
               $i = $i+1;
               $key = array_keys($urls_arr,$v['link']);
               unset($arr[$key[0]]);
            }
        }
        $crawl_num = $crawl_num-$i;
        $go_on = intval($history_num)+$crawl_num >= $scheme['oncenum']?0:1;
        M('history')->where($where)->setInc('num',$crawl_num);
        $result['go_on'] = $go_on;
        sort($arr);//将数组的键名删除，然后从0重建索引
        $result['data'] = $arr;
        return $result;
    }
    
    public function crawlcontent(){
        $sort_id = I('post.category','','intval');
        $url = I('post.url');
        if(!$sort_id){
            $json['alertinfo'] = "请选择栏目";
            $this->ajaxReturn($json);
        }
        if(!$url){ exit;}
        if(IS_AJAX){
            $sf = array(
                'ifeng'=>array(
                    'content'=>'#main_content p'
                ),
                'qq'=>array(
                    'content'=>'#Cnt-Main-Article-QQ p'
                ),
                'weixin'=>array(
                    'title'=>'#activity-name',
                    'content'=>'#js_content p'
                ),
                'sina'=>array(
                    'content'=>'#artibody p',
                    'title'=>'#artibodyTitle'
                ),
                '163'=>array(
                    'content'=>'#endText p',
                ),
                'toutiao'=>array(
                    'content'=>'.article-content p,.article-content div p'
                )
                
            );
            $check['link'] = md5($url);
            if(M('history_list')->where($check)->find()){
                $json['url'] = htmlspecialchars_decode($url);
                $json['info'] = "<span class='pink'>已存在，跳过</span>";
                $this->ajaxReturn($json);
            }
            $current_sf = I('post.sf');
            $dom = $this->_curl($url);
            $data = mb_convert_encoding($dom, 'utf-8', 'GBK,UTF-8,ASCII');
            $data = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">'.$data;
            vendor("HtmlParser/ParserDom");
            $obj = new \ParserDom($data);
            $title_selector = $sf[$current_sf]['title']?$sf[$current_sf]['title']:"h1";
            try{
               $title = $obj->find($title_selector);
               $keywords = $obj->find('meta[name=keywords]');
               $description = $obj->find('meta[name=description]');
               $content = $obj->find($sf[$current_sf]['content']);
               if($title){
                   $detail['title'] = $title[0]->getPlainText();
               }
               if($keywords){
                   $detail['seo_keywords'] = $description[0]->getAttr('content');
               }
               if($description){
                   $detail['description'] = $description[0]->getAttr('content');
               }
               if($content){
                   $detail['content'] = "";
                   foreach ($content as $k=>$v){
                       $detail['content'] .= preg_replace('/href=[\'\"]?[:\/\w#\.]*[\'\"]?/i', '', $v->outerHtml());
                   }
               }
            }catch(Exception $e){}
            //$detail['source'] = $current_sf;
            if($detail['content']){
                //此处根据前台提交的category(栏目ID)，将内容发布到指定的栏目
                if(M('crawl_content')->add($detail)){
                    $history['link'] = md5($url);
                    $history['scheme'] = I('post.scheme');
                    M('history_list')->add($history);
                    $json['info'] = "<span class='green'>已入库</span>";
                }else{
                    $json['info'] = "<span class='blue'>系统错误</span>";
                }
            }else{
                $json['info'] = "<span class='red'>无内容，跳过</span>";
            }
            $json['url'] = htmlspecialchars_decode($url);
            $this->ajaxReturn($json);
        }
    }
    public function editscheme(){
        $where['id'] = I('get.id','','intval');
        if($where['id']==0) $this->error ('非法请求');
        $scheme = M('scheme_keyword')->where($where)->find();
        $sourcefrom = C('SOURCE_FROM');
        $this->assign('scheme',$scheme);
        $this->assign('sourcefrom',$sourcefrom);
        $this->display();
    }
    
    public function goeditscheme(){
        if(IS_POST){
            $where['id'] = I('post.id','','intval');
            $type = I('post.type');
            if($type == 'keyword'){
                $data['keyword'] = I('post.keyword');
                if(!$data['keyword']) $this->error ('关键字不能为空');
                $data['oncenum'] = I('post.oncenum','','intval');
                if($data['artnum']==0) $data['artnum'] = 100;
                $data['sourcefrom'] = I('post.sourcefrom');
                if(!$data['sourcefrom']) $this->error ('请至少选择一个内容来源');
            }elseif($type == 'url'){
                
            }
            $db = M("scheme_".$type);
            
            if($db->where($where)->save($data)){
                $this->success('修改成功');
            }else{
                $this->error('失败');
            }
            
        }
    }

    public function delscheme(){
        $where['id'] = I('get.id','','intval');
        if(!$where['id']) $this->error ('非法请求');
        if(M('scheme_keyword')->where($where)->delete()){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }

    //curl抓取页面
    //@param url
    //@return str
    private function _curl($url){
        $userAgent = 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/43.0.2357.124 Safari/537.36';   
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($curl, CURLOPT_USERAGENT, $userAgent);
        $data = curl_exec($curl);
        curl_close($curl);
        return $data;
    }
}