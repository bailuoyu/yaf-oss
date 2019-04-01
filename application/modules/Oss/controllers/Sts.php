<?php
/*
 * 2018-10-28 cat
 * 用作获取sts授权,注意大小写
 */
class StsController extends CommonController {
    
    protected function init(){
        //鉴权
        $this ->checkToken();
        
    }
    
    //获取sts授权token
    public function tokenAction(){
        //获取参数至属性params
        $params = $this ->parseParams();
        //获取配置类
        $Config = Yaf\Application::app() -> getConfig();
        $r['role'] = $this->role; 
        //处理参数application
        if(!$params['application']){
            $this ->rError(45271);
        }else{
            if(strstr($params['application'],',')){
                $this ->rError(46919);
            }else{
                $r['application'] = $params['application'];
            }
            if(!strstr($this->RoleConfig->OssApplication,$r['application'])){
                $this ->rError(46926);
            }
        }
        //处理参数ownership
        if($params['ownership']=='private'){
            $r['ownership'] = 'private';
        }else{
            $r['ownership'] = 'public';
        }
        $r['sts_path'] = $r['ownership'].'/'.$r['application'].'/';
        //处理参数expire
        $expire = (int)$params['expire'];
        if(!$expire){
            $expire = 3600;
        }else{
            $expire = getRange(600, 86400, $expire);
        }
//        //处理参数tid
//        if(in_array($params['application'],array('www','admin'))){
//            $tid = (int)$params['tid'];
//            if(!$tid){
//                $this ->rError(41838);
//            }
//            $r['sts_path'] .= 't'.$tid.'/';
//        }
        //处理参数relative_path
        $relative_path = trim((string)$params['relative_path'],'//');
        if(strstr($relative_path,'..')){
            $this ->rError(46536);
        }
        $r['sts_path'] .= $relative_path;
        //处理参数power
        if(!$params['power']){
            $r['power'] = 'read,upload';
        }else{
            $r['power'] = strIntersect($Config -> StsPower,$params['power']);
            if(!$r['power']){
                $this ->rError(43420);
            }
        }
        //处理参数allow_format
        $OssFormat = $Config -> OssFormat;
        if(!$params['allow_format']){
            $this ->rError(45983);
        }
        $allow_format_r = array();
        foreach($params['allow_format'] as $_afk => $_afv){
            if(!$OssFormat -> $_afk){
                $this ->rError(48680);
            }elseif($_afv=='all'){
                $allow_format_r[$_afk] = $OssFormat -> $_afk;
            }else{
                $allow_format_r[$_afk] = strIntersect($OssFormat -> $_afk,$_afv);
            }
        }
        $r['allow_format'] = implode(',',$allow_format_r);
        //处理参数max_size
        if(!$params['max_size']){
            $r['max_size'] = 20480;
        }else{
            $r['max_size'] = getRange(10, 512000, (int)$params['max_size']);
        }
        //处理参数total_size
        if(!$params['total_size']){
            $r['total_size'] = 500;
        }else{
            $r['total_size'] = getRange(100, 5120000, (int)$params['total_size']);
        }
        //声明redis模型
        $StsAuth = new Redis\StsAuthModel();
        //设置token
        $stsToken = randomStr(32);
        //需要缓存的参数
        if($StsAuth ->tokenSet($stsToken, $r, $expire+60)){     //加60秒补正网络延时
            $back = array(
                'stsToken' => $stsToken,
                'expire' => $expire
            );
            $back['data'] = $r;
            $this ->rSuccess($back);
        }else{
            $this ->rError(43637);
        }
    }
    
    public function persistAction(){
        //获取参数至属性params
        $params = $this ->parseParams();
        //验证sts_token
        if(!$params['sts_token']){
            $this -> rError(45936);
        }
        //声明redis模型
        $StsAuth = new Redis\StsAuthModel();
        $redis_key = $StsAuth -> getKey($params['sts_token'],'token');
        $stsinfo = $StsAuth::redis() -> hMGet($redis_key,array('ownership','application','sts_path','files'));
        if(!$stsinfo['sts_path']){
            $this -> rError(48943);
        }elseif(!$stsinfo['files']){
            $this -> rError(48379);
        }
        //持久化路径
        if($params['relative_path']){
            $persist_path = $stsinfo['sts_path'].'/'.$params['relative_path'];
        }else{
            $persist_path = $stsinfo['sts_path'];
        }
        //检查文件
        if(is_array($params['files'])){
            $file_r = $params['files'];
        }else{
            $file_r = explode(',',$params['files']);
        }
        $file_r = str_replace($stsinfo['sts_path'],'',$file_r);
        $all_file_r = explode(',',$stsinfo['files']);
        $compare_r = array_intersect($file_r,$all_file_r);
        if($compare_r!=$file_r){
            $this -> rError(44597);
        }
        //获取配置类
        $Config = Yaf\Application::app() -> getConfig();
        $oss_path = $this->RoleConfig -> OssPath;
        //声明文件操作类
        $FileUtil = new File\FileUtil();
        $persist_files = array();   //实际持久化的文件，不会包含已经持久化的文件
        foreach ($file_r as $_v) {
            $real_cache_path = $oss_path.'cache/'.$stsinfo['sts_path'].'/'.$_v;
            $real_path = $oss_path.$persist_path.'/'.$_v;
            try{
                $res = $FileUtil ->moveFile($real_cache_path,$real_path);
                if($res){
                    $persist_files[] = $_v;
                }elseif(file_exists($real_path)){
                }else{
                    $this -> rError(42283,[],"文件{$_v}已被删除");
                }
            }catch(Exception $e){
                $this -> rError(46535);
            }
        }
        $back = array(
            'oss_host' => $Config -> OssHost,
            'persist_patch' => $persist_path,
            'persist_files' => $persist_files
        );
        $this ->rSuccess($back);
    }
}
