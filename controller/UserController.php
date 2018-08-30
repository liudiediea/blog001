<?php
namespace controller;

use models\User;

class UserController{

    public function hello(){

        $user = new User;
        $name = $user->getName();

        return view('users.hello',[
            'name'=>$name,
        ]);
        
    }

    public function world(){

        echo "helloworld";
    }
}