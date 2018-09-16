<?php
namespace controller;
use models\Blog;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class BlogController{

    //日志列表
    public function index(){

        $blog = new BLog;
        $data = $blog->search();

        view('blogs.index',$data);
       
    }
    //为日志生成详情页
    public function content_to_html(){
        $blog = new Blog;
        $blog->content2html();

        $display =  $blog->getDisplay($id);

        // 返回多个数据时必须要用 JSON

        echo json_encode([
            'display' => $display,
            'email' => isset($_SESSION['email']) ? $_SESSION['email'] : ''
        ]);
    }
    public function index2html(){
        $blog = new Blog;
        $blog->index2html();
    }


    public function display()
    {
        //接收日志id
        $id = (int)$_GET['id'];
     
        $blog = new Blog;
        //把浏览量+1 并输出 （如果内存中没有就查询数据库，如果内存有就直接操作）
        $display= $blog->getDisplay($id);

        //返回多个数据时候  必须使用 JSON
        echo json_encode([
            'display' => $display,
            'email' => isset($_SESSION['email']) ? $_SESSION['email'] : ''
        ]);

    }
    public function displayToDb(){
        $blog = new Blog;
        $blog->displayToDb();
    }

    public function create(){
        view('blogs.create');
    } 
    public function store(){
        $title = $_POST['title'];
        $content = $_POST['content'];
        $is_show = $_POST['is_show'];

        $blog = new Blog;
        $id = $blog->add($title,$content,$is_show);
        //如果日志是公开的就生成静态页
        if($is_show == 1){
            $blog->makehtml($id);
        }

        //跳转
        message('发表成功',2,'/blog/index');
    }
    public function delete()
    {
        $id = $_POST['id'];

        $blog = new Blog;
        $blog->delete($id);

        //静态页删除
        $blog->delhtml($id);

        message('删除成功',2,'/blog/index');
        
    }
    public function edit()
    {
        $id = $_GET['id'];
        // 根据ID取出日志的信息

        $blog = new Blog;
        $data = $blog->find( $id );

        view('blogs.edit', [
            'data' => $data,
        ]);

    }
    public function update()
    {
        $title = $_POST['title'];
        $content = $_POST['content'];
        $is_show = $_POST['is_show'];
        $id = $_POST['id'];
        
        $blog = new Blog;
        $blog->update($title, $content, $is_show, $id);

        //如果日志是公开的就重新生成静态页
        if($is_show == 1){
            $blog->makehtml($id);
        }
        else{
            //如果改成私有 要把原来的静态页删掉
            $blog->delhtml($id);
        }

        message('修改成功！', 2, '/blog/index');
    }

    //显示私有日志
    public function content(){
        //1.接收id  取出日志信息
        $id = $_GET['id'];
        $model = new Blog;
        $blog = $model->find($id);

        //2.判断这个日志是不是我的
        if($_SESSION['id'] != $blog['user_id'])
            die('无权访问');
        
        //3.加载视图
        view('blogs.content',[
            'blog' => $blog,
        ]);
    }
    
    public function makeExcel(){
        // 获取当前标签页
         $spreadsheet = new Spreadsheet();
         // 获取当前工作
         $sheet = $spreadsheet->getActiveSheet();

         // 设置第1行内容
         $sheet->setCellValue('A1', '标题');
         $sheet->setCellValue('B1', '内容');
         $sheet->setCellValue('C1', '发表时间');
         $sheet->setCellValue('D1', '是发公开');

         //取出数据库中的日志
         $model  = new \models\Blog;
         $blogs = $model->getNew();
        
         $i = 2;
         foreach($blogs as $v){           
         $sheet->setCellValue('A'.$i, $v['title']);
         $sheet->setCellValue('B'.$i, $v['content']);
         $sheet->setCellValue('C'.$i, $v['created_at']);
         $sheet->setCellValue('D'.$i, $v['is_show']);
         $i++;
         }
        // 生成 Excel 文件
        $date = date('Ymd');
         $writer = new Xlsx($spreadsheet);
         $writer->save(ROOT . 'excel/'.$date.'.xlsx');

         // 下载文件路径
        $file = ROOT.'excel/'.$date.'.xlsx';
        // 下载时文件名
        $fileName = '最新的20条日志'.$date.'.xlsx';
            
        //告诉浏览器这是一个文件流格式的文件    
        Header ( "Content-type: application/octet-stream" ); 
        //请求范围的度量单位  
        Header ( "Accept-Ranges: bytes" );  
        //Content-Length是指定包含于请求或响应中数据的字节长度    
        Header ( "Accept-Length: " . filesize ( $file ) );  
        //用来告诉浏览器，文件是可以当做附件被下载，下载后的文件名称为$file_name该变量的值。
        Header ( "Content-Disposition: attachment; filename=" . $fileName );    
            
        // 读取并输出文件内容
        readfile($file);

    }
     // 点赞
     public function agreements()
     {
         $id = $_GET['id'];

         // 判断登录
         if(!isset($_SESSION['id']))
         {
             echo json_encode([
                 'status_code' => 403,
                 'message' => '必须先登录'
             ]);
             die();
         }
 
         // 点赞
         $model = new \models\Blog;
         $ret = $model->agree($id);
         if($ret)
         {
             echo json_encode([
                 'status_code' => 200,
             ]);
         }
         else
         {
             echo json_encode([
                 'status_code' => '403',
                 'message' => '已经点赞过了'
             ]);
         }
     }
     //点赞列表
     public function agreelist(){
         $id = $_GET['id'];
        
         $model = new \models\Blog;
         $data = $model->agreelist($id);

         echo json_encode([
             'status_code'=>200,
             'data' => $data,
         ]);
     }
 }
    
