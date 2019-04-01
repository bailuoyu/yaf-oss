<?php
/*
 * cli命令行
 * 此文件会在nginx里是不被允许访问的文件，**为第一重保险**
 */
//只允许cli模式运行，**为第二重保险**
if(php_sapi_name()!='cli'){
    echo 'No authority';exit();
}
//指向项目目录
define('APP_PATH',realpath(__DIR__.'/'));
//加载框架的配置文件
$app = new Yaf\Application(APP_PATH.'/conf/'.ini_get('yaf.environ').'/application.ini','cli');     //载入cli的配置
//加载cli的bootstrap配置内容
$app -> bootstrap();

//检查argv参数，**为第三重保险**
$index = strrpos($argv[1],'/');
if(!$index){
    echo 'Func error!';exit();
}
$method = substr($argv[1],$index+1);
$class_name = '\\command\\'. str_replace('/','\\',substr($argv[1],0,$index));
$Class = new $class_name();
$params = array_slice($argv,2);

//运行类中的方法
$app -> execute(array($Class,$method),...$params);

