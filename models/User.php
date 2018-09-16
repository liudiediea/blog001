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
            $_SESSION['money'] = $user['money'];
            $_SESSION['avatar'] = $user['avatar'];
            return true;
        }
        else{
            return false;
        }
    }
    //为用户增加金额
    public function addMoney($money, $userId){
        $stmt = self::$pdo->prepare("UPDATE users SET money=money+? WHERE id=?");
         return $stmt->execute([
            $money,
            $userId,
        ]);

        
    } 
    //获取金额
    public function getMoney(){
        $id = $_SESSION['id'];
        //查询数据库
        $stmt = self::$pdo->prepare('SELECT money FROM users WHERE id = ?');
        $stmt->execute([$id]);
        $money = $stmt->fetch(PDO::FETCH_COLUMN); 
        //更新到session中
        $_SESSION['money'] = $money;
        return $money;
    }
    
     public function setAvatar($path){
        $stmt = self::$pdo->prepare('update users set avatar = ? where id = ?');
        $stmt->execute([
            $path,
            $_SESSION['id'],
        ]);
     }

     public function getAll(){
        $stmt = self::$pdo->query('select * from users');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
   
     
}