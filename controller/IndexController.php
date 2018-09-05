<?php
namespace controller;

class IndexController{
    function index(){
       view('index.index');
    }

    public function info(){
        echo phpinfo();
    }
}