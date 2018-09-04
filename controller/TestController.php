<?php
namespace controller;
class TestController{
    public function register(){
        
        //发邮件
        $redis = new\Predis\Client([
            'scheme' => 'tcp',
            'host'=>'127.0.0.1',
            'port'=>6379,
        ]);
        //注意队列的信息
        $data =[
            'email'=>'1542558263@qq.com',
            'title'=>'biaoti',
            'content'=>'neirong',
        ];

        //数组转为JSON
        $data = json_encode($data);
        $redis->lpush('email',$data);
        echo "注册成功";
    }

    public function email(){
        ini_set('default_socket_timeout',-1);
        echo "邮件已经启动。。。。等待中。。。";

        $redis = new\Predis\Client([
            'scheme' => 'tcp',
            'host'=>'127.0.0.1',
            'port'=>6379,
        ]);
        while(true){
            $data = $redis->brpop('email',0);
            echo "开始发邮件";

        }
        
       
    }
    public function mail(){
        // 设置邮件服务器账号
        $transport = (new \Swift_SmtpTransport('smtp.126.com', 25))  // 邮件器服务IP地址和端口号
        ->setUsername('czxy_qz@126.com')       // 发邮件账号
        ->setPassword('12345678abcdefg');      // 授权码

        // 创建发邮件对象
        $mailer = new \Swift_Mailer($transport);

        // 创建邮件消息
        $message = new \Swift_Message();

        $message->setSubject('测试标题')   // 标题
                ->setFrom(['czxy_qz@126.com' => '全栈1班'])   // 发件人
                ->setTo(['1542558263@qq.com', '1542558263@qq.com' => '刘喋喋'])   // 收件人
                ->setBody('Hello <a href="http://localhost:9999">点击激活</a> World ~', 'text/html');     // 邮件内容及邮件内容类型

        // 发送邮件
        $ret = $mailer->send($message);
        var_dump($ret);
    }

    public function testmail(){
        $mail = new \libs\Mail;
        $mail->send('测试mail类标题','测试mail类内容',['1542558263@qq.com','liuhaha']);
    }
}
