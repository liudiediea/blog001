<?php
namespace controller;

class IndexController{
    function index(){

       $blog = new \models\Blog;
       $blogs = $blog->getNew();

       view('index.index',[
           'blogs'=>$blogs
       ]);
    }

    public function info(){
        echo phpinfo();
    }
}