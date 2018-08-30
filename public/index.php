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

        //如果传了数组 就把数组展开
        if($data){
            //extract 可以把一个数组转为多个变量
            extract($data);
        }
        //加载视图文件
        require_once ROOT . 'views/'.str_replace('.','/',$file).'.html';
    }