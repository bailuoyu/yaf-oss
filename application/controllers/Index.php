<?php
class IndexController extends CommonController {
    
    public function indexAction() {  //默认Action
       echo 'Hello World';
       exit();
//       $this->getView()->assign('content','Hello World');
    }
    
    public function testAction(){
       echo 'Hello Yaf';
    }
    
    public function test2Action(){
        
    }
    
}

