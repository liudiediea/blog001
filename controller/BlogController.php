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
        $id = $blog->add($title,$content,$is_show);
        //如果日志是公开的就生成静态页
        if($is_show == 1){
            $blog->makehtml($id);
        }

        //跳转
        message('发表成功',2,'/blog/index');
    }
    public function delete()
    {
        $id = $_GET['id'];

        $blog = new Blog;
        $blog->delete($id);

        message('删除成功',2,'/blog/index');
        
    }
    public function edit()
    {
        $id = $_GET['id'];
        // 根据ID取出日志的信息

        $blog = new Blog;
        $data = $blog->find( $id );

        view('blogs.edit', [
            'data' => $data,
        ]);

    }
    public function update()
    {
        $title = $_POST['title'];
        $content = $_POST['content'];
        $is_show = $_POST['is_show'];
        $id = $_POST['id'];
        
        $blog = new Blog;
        $blog->update($title, $content, $is_show, $id);

        //如果日志是公开的就重新生成静态页
        if($is_show == 1){
            $blog->makehtml($id);
        }
        else{
            //如果改成私有 要把原来的静态页删掉
            $blog->delhtml($id);
        }

        message('修改成功！', 0, '/blog/index');
    }
 }
    
