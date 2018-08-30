<?php
//定义常量
            //获取当前文件的路径
define('ROOT',dirname(__FILE__).'/../');

//类的自动加载
function autoload($class){

  
    
    $path = str_replace('\\','/',$class);
    require ROOT.$path.'.php';

    
}
    //注册加载函数
    spl_autoload_register('autoload');

  $usercontroller = new controller\UserController;
  $usercontroller->hello();


  //第一参数：要加载的视图文件的 文件名
  //第二参数：想视图中传递的数组
  function view($viewFileName,$data = [ ]){

     // extract 可以把一个数组转为多个变量
      extract($data);

   
     // 加载视图文件
      $path = str_replace('.','/',$viewFileName);
      require(ROOT.'views/'.$path.'.html');
    // echo ROOT.'views/'.$path;
   
  
    }
    