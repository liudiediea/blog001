<?php
namespace controller;
use libs\Mail;

class MailController{
    public function send(){
        
        //链接redis
        $redis = \libs\Redis::getInstance();
            
        $mailer = new Mail;
        ini_set('default_socket_timeout',-1);
        echo "消息队列启动成功";
       while(true){
            //1.先从队列中取出消息
        //                          0代表没有消息就堵塞 一直等待 直到有消息
        /*
            $data=[
                'email',
                '消息的  JSON 字符串'
            ]
        */
        $data = $redis->brpop('email',0);
        
       
        //取出消息 反序列化 转为数组
        //json_decode() 默认转成对象 加上true 转成对象
        $message = json_decode($data[1],true);
         //2.发邮件
        $mailer->send($message['title'],$message['content'],$message['from']);

        echo " 发送成功 等待下一个 \r\n";
       }

    }
}