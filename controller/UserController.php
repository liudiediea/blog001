<?php
namespace controller;

use models\User;
use models\Order;

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

    public function charge(){
        view('users.charge');
    }

    public function docharge(){
        //生成订单
        $money = $_POST['money'];
        $model = new Order;
        $model->create($money);

        message('充值订单已经生成', 2, '/user/order');

    }

    public function order(){

        $order = new Order;
        $data = $order->search();
        // echo '<pre>';
        // var_dump($data);
        // die;
        view('users.order',$data);
        
    }
    public function money(){
        $user = new User;
        echo $user->getMoney();
    }
    //订单状态
    public function orderStatus()
    {
        $sn = $_GET['sn'];
        $try = 10;
        $model = new Order;
       
        do{
            //查询订单信息
            $info = $model->findBySn($sn);
            //如果订单未支付 就等待1秒 并减少尝试的次数 如果已经支付就退出循环
            if($info['status'] == 0){
                sleep(1);
                $try --;
            } 
            else
                break;

            }while($try>0); //如过尝试次数达到指定的系数就退出循环
            echo $info['status'];  
     }

     //上传头像
     public function avatar(){
        view('users.avatar');
     }

     public function setavatar(){
        
        $upload = \libs\Upload::getInstance();
        $path = $upload->upload('avatar', 'avatar');

        //保存到user表中
        $model = new \models\User;
        $model->setAvatar('/uploads/'.$path);

        //删除原图片
        @unlink(ROOT .'public'.$_SESSION['avatar']);
        //设置新头像
        $_SESSION['avatar'] = '/uploads/'.$path;

        message('设置成功', 2, '/blog/index');

        echo $path;


     }

     //批量上传
     public function album(){
         view('users.album');

     }
     public function setablum(){
        
        //1.创建目录
        $root = ROOT.'public/uploads/'; // 根目录
        $date = date('Ymd');    //今天日期
        //判断有没有这个目录  没有就创建
        if(!is_dir($root.$date)){
            mkdir($root.$date, 0777);
        }

        foreach($_FILES['images']['name'] as $k=>$v){
             //2.生成唯一的文件名
             $name = md5(time() . rand(1,9999));
             //补上文件后缀  (字符串截取)
             $ext = strrchr($v,'.');
             $name = $name.$ext;

             //3.移动图片
            move_uploaded_file($_FILES['images']['tmp_name'][$k],$root.$date.'/'.$name);
            echo $root.$date.'/'.$name.'<hr>';
        }     
     }

     //合并图片
     public function mergeimg(){
          /* 接收提交的数据 */
          $count = $_POST['count'];  // 总的数量
          $i = $_POST['i'];        // 当前是第几块
          $size = $_POST['size'];   // 每块大小
          $img = $_FILES['img'];    // 图片
          $name = 'big_img_'.$_POST['img_name'];  // 所有分块的名字
          
          move_uploaded_file( $img['tmp_name'] , ROOT.'tmp/'.$i);
        
          //上传图片添加到redis中
          $redis = \libs\Redis::getInstance();
          //每上传一张图片就把redis中conn_id 加1
          $conn = $redis->incr($name);
         
          if($conn == $count){
              //创建并打开最终的大文件
              $fp = fopen(ROOT.'public/uploads/big/'.$name.'.png','a');
              var_dump($fp);
              //循环所有的分片
              for($i=0; $i<$count; $i++){
                  //读取第i号文件 并写入到 big 文件夹中
                fwrite($fp, file_get_contents(ROOT.'tmp/'.$i));
                unlink(ROOT.'tmp/'.$i);

              }
              
            fclose($fp);
            $redis->del($name);
          }
     }
    

    
}