<?php
/*
 * 2018-10-25 cat
 * 通用方法
 */
abstract class CommonController extends Yaf\Controller_Abstract {
    
    public $params = array();    
    /*
     ** 解析请求参数并赋予给属性$params，raw请求体为json
     * 不支持复合请求方式
     * @only_r 只获取部分参数，为空则全部获取
     */
    protected function parseParams(array $only_r=array()){
        if($this -> params){

        }else{
            $method = $this -> getRequest() -> getMethod();
            switch($method){
                case 'GET':
                    $params = $this -> getRequest() -> getQuery();
                    break;
                case 'POST':
                    $params = $this -> getRequest() -> getPost();
                    //如果使用的是请求体
                    if(!$params){
                        $req = $this -> getRequest() -> getRaw();
                        $params = $this -> checkJson($req);
                    }
                    break;
                case 'PUT':
                    $req = file_get_contents('php://input');
                    $params = $this -> checkJson($req);
                    break;
                case 'PATCH':
                    $req = file_get_contents('php://input');
                    $params = $this -> checkJson($req);
                    break;
                case 'DELETE':
                    $params = $this -> getRequest() -> getQuery();
                    break;
                default :
                    $this -> rError(40102);
            }
            $this -> params = $params;
        }
        
        if($only_r){
            $only_params = array();
            copyKeyValue($only_params,$this -> params,$only_r);
            return $only_params;
        }else{
            return $this -> params;
        }
    }
    
    /*
     ** 检查请求参数格式
     * 是json就解析，返回最终结果
     */
    protected function checkJson($req){
        if(!$req){  //如果没有参数
            return array();
        }else{    //尝试json解析
            $res = json_decode($req,true);
            if(!$res){
                $this -> rError(40103);     //非法格式返回错误
            }
            return $res;
        }
    }

    /*
     ** 返回成功并退出脚本
     */
    protected function rSuccess($data=array(),$msg=''){
        $code = 10000;
        if(!$msg){
            $msg = 'success';
        }
        $this -> result($code,$data,$msg);
    }
    
    /*
     ** 返回错误并退出脚本
     */
    protected function rError($code=40000,$data=array(),$msg=''){
        if($code<40000){
            $code = 40000;
        }
        $ErrorCode  = new \Yaf\Config\Ini(APP_PATH.'/conf/errorcode.ini');
        if($code<41000){
            $msg = $ErrorCode -> Common -> $code;
        }else{
            //控制器名称
            $Module = $this -> getModuleName();   //模块名
            $msg = $ErrorCode -> $Module -> $code;
        }
        
        if($msg){
        }else{
            $msg = 'unknown error';
        }
        $this -> result($code,$data,$msg);
    }
    
    /*
     ** 允许跨域并返回json结果
     */
    protected function result($code,$data,$msg=''){
        $response = $this->getResponse();
        //允许跨域
        $response->setHeader('Access-Control-Allow-Origin','*');    //允许所有来源访问 
        $response->setHeader('Access-Control-Allow-Method','GET,POST,PUT,PATCH,DELETE,HEAD,OPTIONS');  //允许访问的方式
        $response->setHeader('Access-Control-Allow-Headers','Origin,X-Requested-With,Content-Type,Accept,Authorization');   //允许的自定义头参数
        //返回json结果
        $result = array(
            'code' => $code,
            'msg'  => $msg,
            'time' => $this->getRequest()->getServer('REQUEST_TIME'),
            'data' => $data
        );
        $response->setBody(json_encode($result,JSON_UNESCAPED_UNICODE));
        $response->response();
        exit();
    }
    
    /*
     ** 检查Token
     */
    protected function checkToken(){
        $Module = $this -> getModuleName();
        switch($Module){
            case 'Oss':
                $this -> checkOssToken();
                break;
            case 'Sts':
                $this -> checkStsToken();
                break;
            default :
                $this -> rError(40101);
        }
    }
    
    /*
     ** 检查OSS的Token
     */
    protected function checkOssToken(){
        $authorization = $this -> getAuthorization();
        if(!$authorization){
            $this -> rError(40111);
        }
        $Config = \Yaf\Application::app() -> getConfig();
        $role = $Config -> OssAuthorization -> $authorization;
        if($role){
            $this->role = $role;
            $this->RoleConfig = $Config -> $role;
        }else{
            $this -> rError(40112);
        }
    }
    
    public $stsinfo;
    /*
     ** 检查Sts的Token
     */
    protected function checkStsToken(){
        $authorization = $this -> getAuthorization();
        if(!$authorization){
            $this -> rError(40121);
        }
        //声明redis模型
        $StsAuth = new Redis\StsAuthModel();
        $redis_key = $StsAuth -> getKey($authorization,'token');
        $this -> stsinfo = $StsAuth::redis() -> hGetAll($redis_key);
        if(!$this -> stsinfo){
            $this -> rError(40122);
        }
        $this -> stsinfo['redis_key'] = $redis_key;
        $role = $this -> stsinfo['role'];
        $this->RoleConfig = \Yaf\Application::app() -> getConfig() -> $role;
    }
    
    /*
     ** 获取Authorization
     */
    protected function getAuthorization(){
        $authorization = $this -> getRequest() -> getServer('HTTP_AUTHORIZATION');
        //如果是apache屏蔽了Authorization
        if(!$authorization&&function_exists('getallheaders')){
            $headers = getallheaders();
            $authorization = $headers['Authorization']??$headers['AUTHORIZATION'];
        }
        return $authorization;
    }
}
