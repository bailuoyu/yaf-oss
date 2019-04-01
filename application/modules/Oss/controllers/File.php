<?php
/*
 * 2018-11-06 cat
 * 用作Oss文件上传
 */
class FileController extends CommonController{
    
    protected function init(){
        //鉴权
        $this ->checkToken();
    }
    
    /*
     * 上传文件
     */
    public function uploadAction(){
        $file_r = $this -> getRequest() -> getFiles('file');
        if(!$file_r){
            $this ->rError(42064);
        }
//        $this ->rSuccess($file_r);
        //获取参数至属性params
        $params = $this ->parseParams();
        //获取配置类
        $Config = Yaf\Application::app() -> getConfig();
        //处理参数application
        if(!$params['application']){
            $this ->rError(42237);
        }else{
            if(strstr($params['application'],',')){
                $this ->rError(47338);
            }else{
                $r['application'] = $params['application'];
            }
            if(!strstr($this->RoleConfig->OssApplication,$r['application'])){
                $this ->rError(46012,$this->RoleConfig->OssApplication);
            }
        }
        $path = '';
        //检查是否临时文件
        if($params['expire']=='cache'){
            $path = 'cache/';
        }
        //处理参数ownership
        if($params['ownership']=='private'){
            $r['ownership'] = 'private';
        }else{
            $r['ownership'] = 'public';
        }
        $path .= $r['ownership'].'/'.$r['application'].'/';
        //处理参数tid
        if(in_array($params['application'],array('www','admin'))){
            $tid = (int)$params['tid'];
            if(!$tid){
                $this ->rError(43407);
            }
            $path .= 't'.$tid.'/';
        }
        //处理参数relative_path
        $relative_path = trim((string)$params['relative_path'],'//');
        if(strstr($relative_path,'..')){
            $this ->rError(47140);
        }
        $path .= $relative_path;
        
        $oss_host = $this->RoleConfig -> OssHost;
        $oss_path = $this->RoleConfig -> OssPath;
        
        $file_name = $params['rename']?$params['rename']:$file_r['name'];
        
        $path_name = $path.'/'.$file_name;
        
        $real_path = $oss_path.$path_name;
        //声明文件操作类
        $FileUtil = new File\FileUtil();
        $res = $FileUtil->moveFile($file_r['tmp_name'],$real_path);
        if(!$res){
            $this ->rError(41297);
        }
        $back = array(
            'file_name' => $file_name,
            'path_name' => $path_name,
            'url' => $oss_host.'/'.$path_name,
        );
        $this ->rSuccess($back);
    }
    
    /*
     * 删除文件
     */
    public function deleteAction(){
        //获取参数至属性params
        $params = $this ->parseParams();
        //检查文件
        if(is_array($params['files'])){
            $file_r = $params['files'];
        }else{
            $file_r = explode(',',$params['files']);
        }
        //错误级别
        $error_level = (int)$params['error_level'];
        
        //获取配置类
        $Config = Yaf\Application::app() -> getConfig();
        $oss_path = $this->RoleConfig -> OssPath;
        //声明文件操作类
        $FileUtil = new File\FileUtil();
        $back['delete_files'] = array();
        foreach($file_r as $_v) {
            if(strstr($_v,'..')){
                $this ->rError(44560);
            }
            $file = trim($_v,'/');
            if(!$file){continue;}
            $real_path = $oss_path.$file;
            try{
                $res = $FileUtil->unlinkFile($real_path);
                if($res){
                    $back['delete_files'][] = $_v;
                }elseif($error_level==1){
                    $this ->rError(47501,[],"{$_v}不存在");
                }
            }catch(Exception $e){
                throw new \Exception($e);
            }
        }
        $this ->rSuccess($back);
    }
    
}
