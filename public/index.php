<?php
//指向项目目录
define('APP_PATH',realpath(__DIR__.'/../'));
//加载框架的配置文件
$app  = new Yaf\Application(APP_PATH.'/conf/'.ini_get('yaf.environ').'/application.ini');

//引入composer，暂时不需要用到
//require  '../vendor/autoload.php';

//加载bootstrap配置内容
$app -> bootstrap();
//运行
$app -> run();