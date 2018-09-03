<?php
//定义常量
            //获取当前文件的路径
define('ROOT',dirname(__FILE__).'/../');

//类的自动加载
function autoload($class){
    // echo $class;
    $path = str_replace('\\','/',$class);
    
    require ROOT.$path.'.php';
 
}
    //注册加载函数
    spl_autoload_register('autoload');


  if(isset($_SERVER['PATH_INFO'])){

    $pathInfo = $_SERVER['PATH_INFO'];
    //根据/转成数组
    $pathInfo = explode('/',$pathInfo);
    
    //得到控制器和方法名
    $controller = ucfirst($pathInfo[1]).'Controller';
    $action = $pathInfo[2];
   
  }else{
    //默认控制器和方法
    $controller = 'IndexController';
    $action = 'Index';
  }
 

  $fullController = 'controller\\'.$controller;
//   var_dump('<br>');
//   var_dump($fullController);
  $C = new $fullController;
  $C->$action();



    
  //第一参数：要加载的视图文件的 文件名
  //第二参数：想视图中传递的数组
  function view($viewFileName,$data = [ ]){

     // extract 可以把一个数组转为多个变量
      extract($data);

   
     // 加载视图文件
      $path = str_replace('.','/',$viewFileName);
      require(ROOT.'views/'.$path.'.html');
    // echo ROOT.'views'.$path.'.html';
   
  
    }
    //获取当前URL上的参数 并且还能排除掉某些参数
    function getUrlParams($except = [])
    {
        $ret = '';
    
        foreach($_GET as $k => $v)
        {
            if(!in_array($k, $except))
                $ret .= "&$k=$v";
        }
    
        return $ret;
    }
    