<?php
use Yaf\Bootstrap_Abstract;
//use Yaf\Dispatcher;
/**
 * 所有在Bootstrap类中, 以_init开头的方法, 都会被Yaf调用,
 * 这些方法, 都接受一个参数:Yaf_Dispatcher $dispatcher
 * 调用的次序, 和申明的次序相同
 */

class Bootstrap extends Bootstrap_Abstract {

//    加载应用初始化配置
//    public function _initConfig() {
//        $config = Yaf\Application::app()->getConfig();
//        Yaf\Registry::set('config',$config);
//    }

    //定义应用默认模块和默认的控制器及方法
//    public function _initDefaultName(Dispatcher $dispatcher) {
//        $dispatcher->setDefaultModule("Index")->setDefaultController("index")->setDefaultAction("index");
//    }

    //初始化应用的总的路由配置
//    public function _initRoute(Dispatcher $dispatcher)
//    {
//        $config = new Yaf\Config\Ini(APP_PATH . '/conf/routing.ini');
//        $dispatcher->getRouter()->addConfig($config);
//    }

    //初始化模块自己专属的配置
//    public function _initModules(Yaf\Dispatcher $dispatcher) {
//        $app = $dispatcher->getApplication();
//
//        $modules = $app->getModules();
//        foreach ($modules as $module) {
//            if('index' == strtolower($module)){continue;}
//            require_once $app->getAppDirectory().'/modules'.'/$module'.'/_init.php';
//        }
//    }
    
    /*
     ** 允许跨域
     */
//    public function _initCrossDomain(){
//        header('Access-Control-Allow-Origin:*');    //允许所有来源访问 
//        header('Access-Control-Allow-Method:GET,POST,PUT,PATCH,DELETE,HEAD,OPTIONS');  //允许访问的方式
//        header("Access-Control-Allow-Headers:Origin,X-Requested-With,Content-Type,Accept,Authorization");   //允许的自定义头参数
//    }

    /*
     ** 关闭自动渲染
     */
    public function _initDisableView(Yaf\Dispatcher $dispatcher){
        $dispatcher->disableView();
    }
    
    /*
     ** 加载通用函数
     */
    public function _initLoader(){
        //加载通用函数
        Yaf\Loader::import('functions/common.php');
    }
    
}