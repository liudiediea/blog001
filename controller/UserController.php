<?php
namespace controller;

use models\User;

class UserController{

    public function hello(){

        $user = new User;
        $name = $user->getName();

        return view('users.hello',[
            'name'=>$name,
        ]);
        
    }

    public function world(){

        echo "helloworld";
    }


    public function register(){
        view('users.add');
    }

    public function store(){
        //1.接收表单
        $email = $_POST['email'];
        $pass = md5($_POST['password']);
        
        //2.生成激活码（随机的字符串）
        $code = md5( rand(1,99999) );
       
        //3.保存到redis
        $redis = \libs\Redis::getInstance();
         //序列化(数组转为 JSON 字符串)
         $value = json_encode([
            'email' => $email,
            'password' => $pass,
        ]);
        //键名
        $key = "temp_user:{$code}";
                        //设置过期时间
        $redis->setex($key, 300, $value);

        //4..把消息放到队列中
        $name = explode('@', $email);
        $from = [$email, $name[0]];
        $message = [
            'title'=> '账号激活',
            'content'=> "点击以下链接进行激活：<br> 点击激活：
            <a href='http://localhost:9999/user/active_user?code={$code}'>
            http://localhost:9999/user/active_user?code={$code}</a><p>
            如果按钮不能点击，请复制上面链接地址，在浏览器中访问来激活账号！</p>",  
            'from'=> $from,
        ];
        //把消息转成字符串  json => 序列化
        $message = json_encode($message);
        
        //连接redis
        $redis = \libs\Redis::getInstance();
        $redis->lpush('email', $message);
         echo "OK";

    }
    
    public function active_user(){
        //1.接收激活码
        $code = $_GET['code'];
        //2.到redis中取出账号
        $redis = \libs\Redis::getInstance();
        //拼出名字
        $key = 'temp_user:'.$code;
        //取出数据
        $data = $redis->get($key);
        
        if($data){
            //从 redis中删除激活码
            $redis->del($key);
            //反序列化
            $data = json_decode($data,true);
            //插入数据库中
            $user = new \models\User;
            $user->add($data['email'], $data['password']);
            
        }else{
            die("激活码无效");
        }
       
    }
    public function login(){
            view('users.login');
    }

    public function dologin(){
        $email = $_POST['email'];
        $pass = md5($_POST['password']);
        //使用模型
        $user = new User;
        if($user->login($email,$pass)){
            message('登陆成功', 2, '/blog/index');
        }else{
            message('用户名或者密码错误',1,'/user/login');
        }
    }
    public function logout(){
        $_SESSION = [];
        message('退出成功',2,'/');
    }
}