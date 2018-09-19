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
   
    //活跃用户
    public function active(){
        //取出日志的分值
        $stmt = self::$pdo->query('SELECT user_id,COUNT(*)*5 fz
                                    FROM blogs
                                     WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)
                                      GROUP BY user_id');
        $data = $stmt->fetchAll( PDO::FETCH_ASSOC );
        //取评论的分值
        $stmt = self::$pdo->query('SELECT user_id,COUNT(*)*3 fz
                                     FROM blog_comments
                                      WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)
                                         GROUP BY user_id');
        $data2 = $stmt->fetchAll( PDO::FETCH_ASSOC );
        //取点赞的分值
        $stmt = self::$pdo->query('SELECT user_id,COUNT(*) fz
                                    FROM blog_agree
                                     WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)
                                        GROUP BY user_id');
        $data3 = $stmt->fetchAll( PDO::FETCH_ASSOC );

        //合并数组
        $arr = [];
        foreach($data as $v){
           
            $arr[$v['user_id']] = $v['fz'];

        }
        foreach($data2 as $v)
        {
            if( isset($arr[$v['user_id']]) )
                $arr[$v['user_id']] += $v['fz'];
            else
                $arr[$v['user_id']] = $v['fz'];
        }
        foreach($data3 as $v){

            if(isset($arr[$v['user_id']]))
                $arr[$v['user_id']] += $v['fz'];
            else    
                $arr[$v['user_id']] = $v['fz'];
        }

        arsort($arr);

        $data = array_slice($arr, 0, 20, true);
        
        //从数组中取出所有的  键（user_id）
        $userid = array_keys($data); 
        // var_dump($userid);    [1,2,3,4] => '1,2,3,4'
        //数组转字符串
        $userid = implode(',',$userid);
        $sql = "select id,email,avatar from users where id in($userid)";
          $stmt =  self::$pdo->query($sql);
         $data = $stmt->fetchAll( PDO::FETCH_ASSOC );
        // echo '<pre>';
        // var_dump($data);
        
        $redis = \libs\Redis::getInstance();
        $redis->set('active_users',json_encode($data));

    }
    public function getactive(){
        $redis = \libs\Redis::getInstance();
        $data = $redis->get('active_users');
        //                        true 转为数组  false 转为对象
        return json_decode($data,true);
    }
     
}