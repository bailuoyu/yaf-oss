<?php
/*
 * 2018-10-30
 * Sts授权redis模型
 */

namespace Redis;

class StsAuthModel extends CommonModel{
    
    public $keyr = array(   //该类下有哪些键
        'token' => ['token',3600],   //第一个为缩写(不缩写填相同的)，第二个为默认时效(可以直接更改或setExpire($key,$attr_key)单独设置)
    );
    
    /*
     * 设置token值初始化
     */
    public function tokenSet($key,$arr,$expire=null){
        $redis_key = $this ->getKey($key,'token');
//        var_dump($expire);exit();
        if($expire<=0){
            $expire = $this -> keyr['token'][1];
        }
        $res = self::redis() -> hMset($redis_key,$arr);
        $res_ex = self::redis() -> expire($redis_key,$expire);
        return $res&&$res_ex;
    }
    
}
