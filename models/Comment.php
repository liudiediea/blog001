<?php
namespace models;
use PDO;
class Comment extends Base{

    public function addcomment($content,$blog_id){

        $stmt = self::$pdo->prepare('insert into blog_comments(blog_id,content,user_id) values(?,?,?) ');
        $stmt->execute([
            $blog_id,
            $content,
            $_SESSION['id'],
        ]);
    }

    public function getcomment($id){

        $sql = 'select b.*,u.email,u.avatar 
                 from blog_comments b
                 left join users u on b.user_id = u.id
                 where b.blog_id = ? 
                  order by u.id desc';
                  
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}