<?php
namespace controller;
class TestController{

    public function testPurify(){
        


$content = "你懂 <a href=''></a>  的 <a href=''>小技巧   fdaf<div>fdafd</div> fdsa <script>console.log('abc');</script>";
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
// 过滤
$clean_html = $purifier->purify($content);

echo $clean_html;
}

    public function register(){
        
        //发邮件
        $redis = \libs\Redis::getInstance();
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

        $redis = \libs\Redis::getInstance();
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
    public function testconfig(){
        $re = config('redis');
        $db = config('db');
        echo '<pre>';
        var_dump($re);
        var_dump($db);
    }
    public function testlog(){
       $log = new \libs\Log('email');
        $log->log('发表成功');

    }
}
