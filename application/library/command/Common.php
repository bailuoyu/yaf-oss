<?php
/*
 * 2018-11-12 cat
 * 通用方法
 */
namespace command;

class Common{
    
    /*
     ** 返回成功并退出脚本
     */
    protected function rSuccess($msg=''){
        if($msg){
        }else{
            $msg = 'success';
        }
        print_r($msg);
        exit();
    }
    
    /*
     ** 返回错误并退出脚本
     * @log 是否记录日志
     */
    protected function rError($e='error',$log=false){
        if($log){
            $Log = new \Log\File();
            if(is_string($e)){
                $msg = $e;
            }elseif(is_object($e)){
                $msg = $Log -> objError($e);
            }
            $Class = get_called_class();
            $content = "[{$Class}]".PHP_EOL.$msg;
            $path = 'cli';
            $name = 'error_'.date('Y-d-m');
            $content = '<'.date('Y-d-m H:i:s').'>'.PHP_EOL.$content;
            $Log -> write($path,$name,$content);
        }
        throw new \Error($e);
    }
    
}
