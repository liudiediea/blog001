<?php
namespace models;

use PDO;
class Red extends Base{

    public function create($userId){
        $stmt = self::$pdo->prepare('insert into redbags(user_id) values(?)');
        $stmt->execute([
            $userId,
        ]);
    }
}