<?php
namespace Home\Controller;
use Common\Controller\InitController;
class IndexController extends InitController {
    public function index(){
        /*
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'http://finance.sina.com.cn/20150616/114922444571.shtml');// 设置你需要抓取的URL
        curl_setopt($curl, CURLOPT_HEADER, 1);// 设置header
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);// 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上。
        $data = curl_exec($curl);// 运行cURL，请求网页
        //dump($data);
        curl_close($curl);
        //$pattern = '/<div class=\"article-content([\s\S]*)(<\/div>)/';
       // preg_match($pattern, $data,$content);
        //dump($content);
        vendor("HtmlParser/ParserDom");
        $obj = new \ParserDom($data);
        //$title = $obj->find(".title h1");
        //dump($title[0]->getPlainText());
        //$time = $obj->find('.subtitle .time');
        //dump($time[0]->getPlainText());
        try{
           $content = $obj->find('#artibody p'); 
           //$tit = $obj->find('title2');
           if($content){
               $contenta = "";
               foreach($content as $k=>$v){
                   $out = "<p style='text-indent:2em'>";
                   $out .=trim($v->innerHtml(),"　");
                   $out .="</p>";
                   $contenta .=$out;
               }
           }
           if($tit){
               $tit = $tit[0]->getPlainText();
           }
           
        }catch(Exception $e){
             //echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
        echo($contenta);
        echo "<p>adadadadadad</p>";
        
        //dump($content[0]);
        //$cnode = $content[0]->getChildNode();
        //dump($cnode);
        /*
       $str = "hahah<a href='adadadadadad' target='_blank'>测试</a>---adadadada----<a href='#'>121212121</a>";
        $aa = preg_replace('/href=[\'\"]?[\w#]*[\'\"]?/i', 'href="http://guaguayule.com/"', $str);
        echo ($aa."<img src='http://www.huabian.com/uploadfile/2015/0620/20150620082033624.jpg'>");
         * 
         */
        /*
        $a = "<p>3&#26376;11&#26085;&#65292;&#20013;&#22269;&#35777;&#21048;&#19994;&#21327;&#20250;&#20114;&#32852;&#32593;&#35777;&#21048;&#19987;&#19994;&#22996;&#21592;&#20250;&#22312;&#21271;&#20140;&#25104;&#31435;&#65292;&#36825;&#26159;&#36825;&#20010;&#34892;&#19994;&#33258;&#24459;&#21327;&#20250;&#39318;&#27425;&#25104;&#31435;&#20114;&#32852;&#32593;&#35777;&#21048;&#19987;&#19994;&#22996;&#21592;&#20250;&#65292;&#35777;&#30417;&#20250;&#20027;&#24109;&#21161;&#29702;&#24352;&#32946;&#20891;&#29978;&#33267;&#25552;&#20986;&ldquo;&#29992;3-5&#24180;&#26102;&#38388;&#25226;&#20114;&#32852;&#32593;&#35777;&#21048;&#19994;&#21153;&#20570;&#22823;&#20570;&#24378;&rdquo;&#12290;</p>";
        $b = $this->unicode2utf8($a);
        dump($b);
         * 
         */
        
        $a = "腾讯娱乐讯<img alt='腾讯娱乐'>";
        $b = preg_replace('/(\'?!腾讯).*/',"", $a);
        dump($b);
        echo $b;
    }
    
   
    
}