<?php
namespace libs;

//单例模式：三私一共
class Redis{
    
    private static $redis = null;
    private function __clone(){}
    private function __contruct(){}

    
    public static function getInstance(){
        $config = config('redis');
        //如果没有redis 就生成一个
        if(self::$redis === null){

            self::$redis = new\Predis\Client($config);
       }
       //返回已经有的redis
       return self::$redis;
    }
 
}