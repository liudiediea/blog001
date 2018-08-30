<?php
//定义常量
            //获取当前文件的路径
define('ROOT',dirname(__FILE__).'/../');

//类的自动加载
function autoload($class){

    $path = str_replace('\\','/'.$class);
    require ROOT.$path.'.php';

    
}
    //注册加载函数
    spl_autoload_register('autoload');

    $usercontroller = new controller\UserController;
    $usercontroller->hello();
    
    function view($a,$b){

    }