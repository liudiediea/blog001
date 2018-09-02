<?php
namespace controller;
use PDO;

class BlogController{
    public function index(){

        //链接数据库
        $pdo = new PDO('mysql:host=127.0.0.1;dbname=blog','root','');
        $pdo->exec("SET NAMES utf8");
        
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
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM blogs WHERE $where");
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
        $stmt = $pdo->prepare("select * from blogs where $where ORDER BY $odby $odway LIMIT $offset,$perpage");  
        $stmt->execute($value);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
       
        // var_dump($data);
        // echo "select * from blogs where $where";
        


        //加载视图
        view('blogs.index',[
            'data'=>$data,
            'btns'=>$btns,

        ]);
    }
    
}