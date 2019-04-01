<?php
/**
 * 2018-10-26 *cat*添加
 * 系统方法
 */
class SystemController extends CommonController {
    
    //创建随机的错误代号
    function ErrorAction(){
        $ErrorCode  = new \Yaf\Config\Ini(APP_PATH.'/conf/errorcode.ini');
        $er = $ErrorCode -> toArray();
        $code_r = array(10000 => 'success');
        foreach($er as $_v){
            $code_r = $code_r + $_v;
        }
        $code = null;
        do{
//            $x = mt_rand(41001,48999);
            $x = random_int(41001,48999);
            if(array_key_exists($x,$code_r)){
                
            }else{
               $code =  $x;
            }
        }while(!$code);
        echo 'Error Code:',PHP_EOL,$x;    
    }
    
}
