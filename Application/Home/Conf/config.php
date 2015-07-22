<?php
return array(
	//'配置项'=>'配置值'
    'URL_MODEL' => 0,
    'DEFAULT_CONTROLLER'=> 'crawl',
    'DB_TYPE'           => 'mysql', // 数据库类型
    'DB_HOST'           => 'localhost', // 服务器地址
    'DB_NAME'           => 'spider', // 数据库名
    'DB_USER'           => 'root', // 用户名
    'DB_PWD'            => 'admin', // 密码
    'DB_PORT'           => 3306, // 端口
    'DB_PREFIX'         => 'xc_', // 数据库表前缀 
    'DB_CHARSET'        => 'utf8', // 字符集
    'SOURCE_FROM' =>array(
        "qq"=>array('name'=>'腾讯','url'=>'qq.com'),
        "sina"=>array('name'=>'新浪','url'=>'sina.com.cn'),
        'ifeng'=>array('name'=>'凤凰网','url'=>'ifeng.com'),
        'toutiao'=>array('name'=>'头条新闻','url'=>'toutiao.com'),
        '163'=>array('name'=>'网易','url'=>'163.com'),
        'weixin'=>array('name'=>'微信','url'=>'weixin.sogou.com')
    ),
);