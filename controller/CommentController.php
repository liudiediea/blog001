<?php
namespace controller;


class CommentController{

    public function comment(){

        //接收原始数据
        $data = file_get_contents('php://input');
        $_POST = json_decode($data, true);

        //1.检查用户是否登录
        if(!isset($_SESSION['id'])){
            echo json_encode([
                'status_code'=>'401',
                'message'=>'未登录',
            ]);
            die;
        }

        //2.接收表单中的数据
        $content = e($_POST['content']);
        $blog_id = $_POST['blog_id'];

        //3.插入到评论表中
        $model = new \models\Comment;
        $model -> addcomment($content, $blog_id);

        //4.返回新发表的评论（过滤数据）
        echo json_encode([
            'status_code'=> '200',
            'message'=>'发表成功',
            'data'=> [
                'content' => $content,
                'avatar' => $_SESSION['avatar'],
                'email' => $_SESSION['email'],
                'created_at' => date('Y-m-d H:i:s'),
            ]

        ]);
        exit;
    }

    public function comment_list(){
        
        //1.接收日志id    
        $id = $_GET['id'];

        //2.获取日志评论
        $model = new \models\Comment;
        $data  = $model->getcomment($id);

        //3.转成json
        echo json_encode([
            'status_code'=> 200,
            'data' => $data,
        ]);

    }
}