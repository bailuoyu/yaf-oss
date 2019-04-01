<?php
use Yaf\Bootstrap_Abstract;
//use Yaf\Dispatcher;
/**
 * 所有在Bootstrap类中, 以_init开头的方法, 都会被Yaf调用,
 * 这些方法, 都接受一个参数:Yaf\Dispatcher $dispatcher
 * 调用的次序, 和申明的次序相同
 */

class Bootstrap extends Bootstrap_Abstract {
    
    /*
     * 加载通用函数
     */
    public function _initLoader(){
        //加载通用函数
        Yaf\Loader::import('functions/common.php');
    }
    
}