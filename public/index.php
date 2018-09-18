<?php
    //设置时区
    date_default_timezone_set('PRC');

    //设置SESSION 保存
    ini_set('session.save_handler','redis');
    ini_set('session.save_path','tcp://127.0.0.1:6379?database=3');

    //开启session
    session_start();

    // 如果用户以 POST 方式访问网站时，需要验证令牌(防止外站提交)
// if($_SERVER['REQUEST_METHOD'] == 'POST')
// {
//     if(!isset($_POST['_token']))
//         die('违法操作！');

//     if($_POST['_token'] != $_SESSION['token'])
//         die('违法操作！');
// }


//定义常量
            //获取当前文件的路径
define('ROOT',dirname(__FILE__).'/../');

//引入 composer 自动加载文件
require(ROOT.'vendor/autoload.php');

//类的自动加载
function autoload($class){
    // echo $class;
    $path = str_replace('\\','/',$class);
    
    require ROOT.$path.'.php';
    // echo ROOT.$path.'.php';
}
    //注册加载函数
    spl_autoload_register('autoload');

//添加路由：解析URL浏览器上blog/index CLI中就是 blog index
  if(php_sapi_name()=='cli'){
    
    //得到控制器和方法名
    $controller = ucfirst($argv[1]).'Controller';
    $action = $argv[2];
   
  }else{
    if( isset($_SERVER['PATH_INFO']) )
    {
        $pathInfo = $_SERVER['PATH_INFO'];
        // 根据 / 转成数组
        $pathInfo = explode('/', $pathInfo);

        // 得到控制器名和方法名 ：
        $controller = ucfirst($pathInfo[1]) . 'Controller';
        $action = $pathInfo[2];
    }else{
    //默认控制器和方法
    $controller = 'IndexController';
    $action = 'Index';
  }
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
    
    function config($name)
    {
        static $config = null;
        if($config === null)
        {
            // 引入配置文件 
            $config = require(ROOT.'config.php');
        }
        return $config[$name];
    }
    function redirect($url){
        header('Location:'.$url);
        exit;
    }
    //跳回上一个页面
    function back(){
        redirect($_SESSION['HTTP_REFERER']);
    }

    //提示信息的函数
    function message($message,$type,$url,$seconds =5){
        if($type == 0){
            echo "<script>alert('{$message}');location.href='{$url}';</script>";
            exit;
    
        }else if($type ==1){
            //加载消息页面
            view('common.success',[
                'message' => $message,
                'url'=> $url,
                'seconds'=>$seconds
            ]);
        }else if($type ==2){
            //消息保存到SESSION
            $_SESSION['_MESS_'] = $message;
            //跳转到下一个页面
            redirect($url);
        }

    }
    function e($content){
        return htmlspecialchars($content);
    }

    function hpe($content){
        //一直保存在内存中（直到脚本执行结束）
        static $purifier = null;
        //只有第一次调用时创建新对象
        if($purifier === null){
            // 1. 生成配置对象
        $config = \HTMLPurifier_Config::createDefault();
        // 2. 配置
        // 设置编码
        $config->set('Core.Encoding', 'utf-8');
        $config->set('HTML.Doctype', 'HTML 4.01 Transitional');
        // 设置缓存目录
        $config->set('Cache.SerializerPath', ROOT.'cache');
        // 设置允许的 HTML 标签
        $config->set('HTML.Allowed', 'div,b,strong,i,em,a[href|title],ul,ol,ol[start],li,p[style],br,span[style],img[width|height|alt|src],*[style|class],pre,hr,code,h2,h3,h4,h5,h6,blockquote,del,table,thead,tbody,tr,th,td');
        // 设置允许的 CSS
        $config->set('CSS.AllowedProperties', 'font,font-size,font-weight,font-style,margin,width,height,font-family,text-decoration,padding-left,color,background-color,text-align');
        // 设置是否自动添加 P 标签
        $config->set('AutoFormat.AutoParagraph', TRUE);
        // 设置是否删除空标签
        $config->set('AutoFormat.RemoveEmpty', true);
        // 3. 过滤
        // 创建对象
        $purifier = new \HTMLPurifier($config);

        }
        
        // 过滤
        $clean_html = $purifier->purify($content);
        return $purifier->purify($content);
    }
    function csrf()
    {
    if(!isset($_SESSION['token']))
    {
        // 生成一个随机的字符串
        $token = md5( rand(1,99999) . microtime() );
        $_SESSION['token'] = $token;
    }
  
        
        return $_SESSION['token'];
    }
    //生成令牌隐藏域
    function csrf_field(){
        $csrf = isset($_SESSION['token']) ? $_SESSION['token'] : csrf();
        echo "<input type='hidden' name='_token' value='{$csrf}'>";
    }