<?php 
namespace models;
use PDO;
class Order extends Base{
    
    public function create($money){
        $flake = new \libs\Snowflake(1023);
        $stmt = self::$pdo->prepare('INSERT INTO orders(user_id,money,sn) VALUES(?,?,?)');
        $stmt->execute([
            $_SESSION['id'],
            $money,
            $flake->nextId(),
        ]);
    }
    //搜索订单
    public function search(){
        
         $where = 'user_id='.$_SESSION['id'];
      
        //排序
        $odby = 'created_at';
        $odway = 'desc';

       
        //分页
        $perpage = 15; // 每页15
        // 接收当前页码（大于等于1的整数）， max：最参数中大的值
        $page = isset($_GET['page']) ? max(1,(int)$_GET['page']) : 1;
    
        $offset = ($page-1)*$perpage;

        // 制作按钮
        // 取出总的记录数
        $stmt = self::$pdo->prepare("SELECT COUNT(*) FROM orders WHERE $where");
        $stmt->execute();
        $count = $stmt->fetch( PDO::FETCH_COLUMN );
        // 计算总的页数（ceil：向上取整（天花板）， floor：向下取整（地板））
        $pageCount = ceil( $count / $perpage );

        $btns = '';
        for($i=1; $i<=$pageCount; $i++)
        {
            // 先获取之前的参数
            $params = getUrlParams(['page']);

            $class = $page==$i ? 'active' : '';
            $btns .= "<a class='$class' href='?{$params}page=$i'> $i </a>";
            
        }
        //执行sql
        $stmt = self::$pdo->prepare("select * from orders where $where ORDER BY $odby $odway LIMIT $offset,$perpage");  
        $stmt->execute();
        //取数据
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //加载视图
       return[
        //    'btns'=>$btns,
           'data'=>$data
       ];
    }
    public function findBySn($sn){
        $stmt = self::$pdo->prepare('SELECT * FROM orders WHERE sn=?');
        $stmt->execute([
            $sn
        ]);
        //取数据(以关联数组的结构返回)
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function setPaid($sn){
        $stmt = self::$pdo->prepare('update orders set status=1,pay_time=now() where sn=?');
        //执行sql并把结果返回 成功true 失败 false
        return $stmt->execute([
            $sn,
        ]);
    }
}