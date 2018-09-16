<?php
namespace controller;
class ToolController{

    public function getAllUsers(){
        $model = new \models\User;
        $data = $model->getAll();

        echo json_encode([
            'status_code' => 200,
            'data' => $data,

        ]);
    }
    public function change(){
        if(config('mode') != 'dev'){
            die("非法访问");
        }
        $email = $_GET['email'];

        //退出原来账号
        $_SESSION = [];

        //登陆新账号
        $user = new \models\User;
        $user->login($email,md5('123123'));
    }
}