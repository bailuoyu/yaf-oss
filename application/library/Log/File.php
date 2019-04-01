<?php
/*
 * 2018-11-14 cat
 * 文件日志记录
 */
namespace Log;

class File{
    
    public static $log_directory;
    /*
     * 获取日志根目录
     */
    public function LogDir(){
        if(self::$log_directory){
            
        }else{
            self::$log_directory = \Yaf\Application::app() -> getConfig() -> log -> directory;
            if(!self::$log_directory){
                throw new \Exception('config配置错误');
            }
        }
        return self::$log_directory;
    }

    /*
     * 写日志
     * 
     */
    public function write($path,$name,$content){
        $path = $this ->checkPath($path);
        if(stristr($name,'.log')){
        }else{
            $name = $name.'.log';
        }
        $path_name = $this->LogDir().'/'.$path.'/'.$name;
        //声明文件操作类
        $FileUtil = new \File\FileUtil();
        //如果文件不存在则创建
        $FileUtil ->createFile($path_name);
        $content .= PHP_EOL;    //拼接换行符
        //读写方式打开，追加写
//        try{
            $log_file = fopen($path_name,'a+');
            fwrite($log_file,$content);
            fclose($log_file);
//        }catch(Exception $e){
//            throw new \Exception($e);
//        }
    }
    
    /*
     * 检查路径
     */
    protected function checkPath($path){
        if(strstr($path,'..')){
            throw new \Exception('危险的路径参数');
        }
        return trim($path,'/');
    }
    
    /*
     * 处理抓取类错误Exception，Error
     */
    public function objError($e){
        $msg = $e->getMessage().PHP_EOL.$e->getLine().PHP_EOL.$e->getFile().PHP_EOL.$e->getCode();
        return $msg;
    }
    
}

