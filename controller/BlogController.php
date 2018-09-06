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


    public function display()
    {
        //接收日志id
        $id = (int)$_GET['id'];
        // echo $id;
        // echo" <br/>";
        $blog = new Blog;
        //把浏览量+1 并输出 （如果内存中没有就查询数据库，如果内存有就直接操作）
        echo $blog->getDisplay($id);

    }
    public function displayToDb(){
        $blog = new Blog;
        $blog->displayToDb();
    }

    public function create(){
        view('blogs.create');
    } 
    public function store(){
        $title = $_POST['title'];
        $content = $_POST['content'];
        $is_show = $_POST['is_show'];

        $blog = new Blog;
        $blog->add($title,$content,$is_show);

        //跳转
        message('发表成功',2,'/blog/index');
    }
  
 }
    
