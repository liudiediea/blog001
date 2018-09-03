<?php
namespace controller;
use models\Blog;

class BlogController{

    //日志列表
    public function index(){

        $blog = new BLog;
        $data = $blog->search();

        view('blogs.index',$data);
       
    }
    //为日志生成详情页
    public function content_to_html(){
        $blog = new Blog;
        $blog->content2html();
    }
    public function index2html(){
        $blog = new Blog;
        $blog->index2html();
    }


    public function update_display(){
        //接收日志id
        $id = (int)$_GET['id'];
         //使用日志id拼出键名
        $redis = new\Predis\Client([
        'scheme' => 'tcp',
        'host'=>'127.0.0.1',
        'port'=>6379,
    ]);
    
    $key = "blog-{$id}";
    //判断hash中是否有这个值
    if($redis->hexists('blog_display',$key)){
        //累加并且 返回加完之后的值
        $newNum = $redis->hincrby('blog_display',$key,1);
        echo $newNum;
    }else{
        //从数据库中取出浏览量
        $blog = new Blog;
        $display = $blog->getDisplay($id);
        $display++;
        //加到redis
        $redis->hset('blog_displays',$key,$display);
        echo $display;

    }
  
    }
    
}