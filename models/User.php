<?php
namespace models;
use PDO;
class User extends Base{

    public function add($email,$pass){
        $stmt = self::$pdo->prepare("insert into users (email,password) values(?,?)");
        return $stmt->execute([
            $email,
            $pass,
        ]);
    }
    public function login($email,$pass){
        $stmt = self::$pdo->prepare('select * from users where email = ? and password=?');
        $stmt->execute([
            $email,
            $pass,
        ]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        if($user){
            
            $_SESSION['id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            return true;
        }
        else{
            return false;
        }
    }
}