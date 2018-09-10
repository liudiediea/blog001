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

        $display =  $blog->getDisplay($id);

        // 返回多个数据时必须要用 JSON

        echo json_encode([
            'display' => $display,
            'email' => isset($_SESSION['email']) ? $_SESSION['email'] : ''
        ]);
    }
    public function index2html(){
        $blog = new Blog;
        $blog->index2html();
    }


    public function display()
    {
        //接收日志id
        $id = (int)$_GET['id'];
     
        $blog = new Blog;
        //把浏览量+1 并输出 （如果内存中没有就查询数据库，如果内存有就直接操作）
        $display= $blog->getDisplay($id);

        //返回多个数据时候  必须使用 JSON
        echo json_encode([
            'display' => $display,
            'email' => isset($_SESSION['email']) ? $_SESSION['email'] : ''
        ]);

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
        $id = $_POST['id'];

        $blog = new Blog;
        $blog->delete($id);

        //静态页删除
        $blog->delhtml($id);

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

        message('修改成功！', 2, '/blog/index');
    }

    //显示私有日志
    public function content(){
        //1.接收id  取出日志信息
        $id = $_GET['id'];
        $model = new Blog;
        $blog = $model->find($id);

        //2.判断这个日志是不是我的
        if($_SESSION['id'] != $blog['user_id'])
            die('无权访问');
        
        //3.加载视图
        view('blogs.content',[
            'blog' => $blog,
        ]);
    }
 }
    
