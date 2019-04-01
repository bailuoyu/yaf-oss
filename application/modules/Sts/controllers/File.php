<?php
/*
 * 2018-11-02 cat
 * 用作Sts文件上传,注意大小写
 */
class FileController extends CommonController{
    
    protected function init(){
        //鉴权
        $this ->checkToken();
    }
    
    public function uploadAction(){
        $file_r = $this -> getRequest() -> getFiles('file');
        if(!$file_r){
            $this ->rError(42978);
        }
        //文件大小换算成k
        $file_size = ceil($file_r['size']/1024);
        //剩下可上传的容量大小
        $surplus_size = $this->stsinfo['max_size'] - $this->stsinfo['upload_size'];
        //空间不够
        if($file_size>$surplus_size){
            $this ->rError(48371,[],"空间不足,累积上传文件空间最大{$this->stsinfo['max_size']}k,剩余{$surplus_size}k");
        }
        //根据文件后缀判断文件类型
        $pathinfo = pathinfo($file_r['name']);
        $type = $pathinfo['extension'];
        if(!stristr($this->stsinfo['allow_format'].',',$type)){
            $this ->rError(47478);
        }
        //生成随机文件名
        $new_name = randomStr(32).'.'.$type;
        //获取配置类
//        $Config = Yaf\Application::app() -> getConfig();
        $oss_host = $this->RoleConfig -> OssHost;
        $oss_path = $this->RoleConfig -> OssPath;
        $path_name = $this->stsinfo['sts_path'].'/'.$new_name;
        $cache_path = 'cache/'.$path_name;
        $real_cache_path = $oss_path.$cache_path;     //文件真实路径
        //声明文件操作类
        $FileUtil = new File\FileUtil();
        try{
            $res = $FileUtil->moveFile($file_r['tmp_name'],$real_cache_path);
            if(!$res){
                $this ->rError(46986);
            }
        }catch(Exception $e){
            throw new \Exception($e);
        }
        //声明redis模型
        $StsAuth = new Redis\StsAuthModel();
        $redis_key = $this->stsinfo['redis_key'];
        //修改已经上传文件的累积大小
        $r['upload_size'] = $this->stsinfo['upload_size'] + $file_size;
        //添加已经上传文件的名称
        if($this->stsinfo['files']){    //如果有内容则拼接逗号
            $r['files'] = $this->stsinfo['files'].','.$new_name;
        }else{
            $r['files'] = $new_name;
        }
        if(!$StsAuth::redis() -> hMset($redis_key,$r)){
            $this ->rError(47976);
        }
        $back = array(
            'file_name' => $new_name,
            'path_name' => $path_name,
            'cache_url' => $oss_host.'/'.$cache_path,
//            'persist_url' => $oss_host.'/'.$path_name
        );
        $this ->rSuccess($back);
    }
    
}
