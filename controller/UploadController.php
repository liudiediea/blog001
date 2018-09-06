<?php
namespace controller;

class UploadController{
    public function upload(){
        //接收图片
        $file = $_FILES['image'];
        //生成随机文件名
        $name = time();
        //移动图片
        move_uploaded_file($file['tmp_name'],ROOT.'public/uploads/'.$name.'.png');

        echo json_encode([
            'success'=>true,
            'file_path'=>'/public/uploads/'.$name.'.png',
        ]);
    }
}