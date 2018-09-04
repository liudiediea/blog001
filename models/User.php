<?php
namespace models;
use PDO;
class User{

    public $pdo;
    public function __construct(){
 
        //连接数据库
       $this->pdo = new PDO('mysql:host=127.0.0.1;dbname=blog','root','');
       $this->pdo->exec("SET NAMES utf8");
    }

    public function add($email,$pass){
        $stmt = $this->pdo->prepare("insert into users (email,password) values(?,?)");
        return $stmt->execute([
            $email,
            $pass,
        ]);
    }
}