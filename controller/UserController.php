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
        //2.插入数据库中
        $user = new User;
        $ret = $user->add($email,$pass);

        if(!$ret){
            die("注册失败");
        }
        //3.把消息放到队列中
        $name = explode('@',$email);
        $from = [$email,$name[0]];
        $message = [
            'title'=> '欢迎加入全栈一班',
            'content'=> "点击以下链接:<br> <a href=''>点击激活</a>",
            'from'=> $from,
        ];
        //把消息转成字符串  json => 序列化
        $message = json_encode($message);
        
        //连接redis
        $redis = \libs\Redis::getInstance();

        $redis->lpush('email', $message);
         echo "OK";

    }
}