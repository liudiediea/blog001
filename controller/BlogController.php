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
    
}