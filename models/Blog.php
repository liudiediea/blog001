<?php

namespace Models;
use PDO;

class BLog{
  
     public $pdo;   

     public function __construct(){
 
         //连接数据库
        $this->pdo = new PDO('mysql:host=127.0.0.1;dbname=blog','root','');
        $this->pdo->exec("SET NAMES utf8");
     }

      public function search(){     
        $where = 1;
        $value = [];
        //如果传了 keyword 参数并且值不为空添加where 条件
        if(isset($_GET['keyword']) && $_GET['keyword']){

            $where .= " AND (title like ? OR content like ?)";
            $value[] ='%'.$_GET['keyword'].'%';
            $value[] ='%'.$_GET['keyword'].'%';
            
        }
        //日期字段搜索
        if(isset($_GET['start_date']) && $_GET['start_date']){

            $where .= " AND created_at >= ?";
            $value[] = $_GET['start_date'];
        }
        if(isset($_GET['end_date']) && $_GET['end_date']){

            $where .= " AND created_at <= ?";
            $value[] = $_GET['end_date'];
        }
          // is_show 字段的搜索
          if(isset($_GET['is_show']) && ($_GET['is_show']==1 || $_GET['is_show']==='0'))
          {
             $where .= " AND is_show= ?";
             $value[] = $_GET['is_show'];
      
          }

        //排序
        $odby = 'created_at';
        $odway = 'desc';

        if(isset($_GET['odby']) && $_GET['odby'] == 'display')
        {
            $odby = 'display';
        }

        if(isset($_GET['odway']) && $_GET['odway'] == 'asc')
        {
            $odway = 'asc';
        }
        
        //分页
        $perpage = 15; // 每页15
        // 接收当前页码（大于等于1的整数）， max：最参数中大的值
        $page = isset($_GET['page']) ? max(1,(int)$_GET['page']) : 1;
        // 计算开始的下标
        // 页码  下标
        // 1 --> 0
        // 2 --> 15
        // 3 --> 30
        // 4 --> 45
        $offset = ($page-1)*$perpage;

        // 制作按钮
        // 取出总的记录数
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM blogs WHERE $where");
        $stmt->execute($value);
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
        $stmt = $this->pdo->prepare("select * from blogs where $where ORDER BY $odby $odway LIMIT $offset,$perpage");  
        $stmt->execute($value);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
       
        // var_dump($data);
        // echo "select * from blogs where $where";
        

        //加载视图
       return[
           'btns'=>$btns,
           'data'=>$data,
       ];
    } 
    public function content2html(){
        //取日志的数据
        $pdo = new PDO('mysql:host=127.0.0.1;dbname=blog','root','');
        $pdo->exec('set names utf8');

        $stmt = $pdo->query('select * from blogs');
        $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //开启缓冲区
        ob_start();

        //生成静态页
        foreach($blogs as $v){
            //加载视图
            view('blogs.content',[
                'blog' => $v,
            ]);
            //取出缓冲区的内容
            $str = ob_get_contents();
            //生成静态页
            file_put_contents(ROOT.'public/contents/'.$v['id'].'.html',$str);
            //清空缓存区
            ob_clean();

        }
    }
    public function index2html(){
          // 取 前20 条记录 数据 
          $stmt = $this->pdo->query("SELECT * FROM blogs WHERE is_show=1 ORDER BY id DESC LIMIT 20");
          $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);   
          
          // 开启一个缓冲区
          ob_start();
  
          // 加载视图文件到缓冲区
          view('index.index', [
              'blogs' => $blogs,
          ]);
  
          // 从缓冲区中取出页面
          $str = ob_get_contents();
  
          // 把页面的内容生成到一个静态页中
          file_put_contents(ROOT.'public/index.html', $str);
  
    }
    
//从数据库中取出日志的浏览量
public function getDisplay($id){

     //使用日志id拼出键名
    $key = "blog-{$id}";
    //链接redis
    $redis = new\Predis\Client([
        'scheme' => 'tcp',
        'host'=>'127.0.0.1',
        'port'=>6379,
    ]);

    //判断hash中是否有这个值
    if($redis->hexists('blog_displays',$key)){
        //累加并且 返回加完之后的值
        $newNum = $redis->hincrby('blog_displays',$key,1);
        echo $newNum;
    }else{
        //从数据库中取出浏览量
        $stmt = $this->pdo->prepare('select display from blogs where id=?');
        $stmt->execute([$id]);
        $display = $stmt->fetch(PDO::FETCH_COLUMN);
        $display++;
        //保存到redis
        $redis->hset('blog_displays',$key,$display);
        echo $display;

}
    
}
//把内存中的浏览量写回到数据库中
public function displayToDb(){
    //1.先取出内存中所有的浏览量
    //连接redis
    $redis = new \Predis\Client([
        'scheme' => 'tcp',
        'host' => '127.0.0.1',
        'port' => 6379,
    ]);

    $data = $redis->hegetall('blog_displays');

    //2.更新回数据库
    foreach($data as $k=>$v){
        $id = str_replace('blog-','',$k);
        $sql = "update blogs display={$v} where id ={$id}";
        $this->pdo->exec($sql);
    }
}
}