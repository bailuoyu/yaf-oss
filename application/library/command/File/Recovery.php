<?php
namespace command\File;

class Recovery extends \command\Common{
    
    function ceshi(){
        $this ->rError('这是错误测试',true);
    }
            
    function delCache(){
        //获取配置类
        $Config = \Yaf\Application::app() -> getConfig();
        $role_r = $Config -> OssAuthorization -> toArray();
        foreach($role_r as $role){
            $OssConfig = $Config -> $role;
            $oss_path = $OssConfig -> OssPath;
            if(!strstr(trim($oss_path,'/'),'/')){    //必须至少是二级目录
                $this ->rError('系统级错误');
            }
            $cache_patch = $oss_path.'cache';
            //声明文件操作类
            $FileUtil = new \File\FileUtil();
            $timeout = $OssConfig -> OssCacheTimeout;      //删除一周以前的文件
            try{
                $FileUtil -> clearFileByTime($cache_patch,$timeout);
                $this ->rSuccess();
            }catch(Exception $e){
                $this ->rError($e);
            }
        }
    }
    
}
