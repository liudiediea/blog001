<?php
namespace Controller;
class RedController{
    
    public function view(){
        view('redbag.rob');
    }
    public function rob(){
        //1.判断是否登录
        if(!isset($_SESSION['id'])){
            echo json_encode([
                'status_code' => '401',
                'message'=> '未登录',
            ]);
            exit;
        }

        //2.判断当前时间  是否是9-10点
       
        if( date('H') <9 ){
            echo json_encode([
                'status_code'=>'403',
                'message'=>'没有到时间',
            ]);
            exit;
        }

        //3.判断今天是否已经抢过了
        $key = 'redbag_'.date('Ymd');
        $redis = \libs\Redis::getInstance();
        $has = $redis->sismember($key, $_SESSION['id']);
        if($has){
            echo json_encode([
                'status_code'=>'403',
                'message'=>'今天已经抢过了',
            ]);
            exit;
        }
        //4.判断库存量
        $stock = $redis->decr('redbag_stock');
        if($stock < 0){
            echo json_encode([
                'status_code'=>'403',
                'message'=>'库存不足',
            ]);
            exit;
        }

        //5.下订单
        $redis->lpush('redbag_order', $_SESSION['id']);

        $redis->sadd($key,$_SESSION['id']);
        echo json_encode([
            'status_code'=>'200',
            'message'=>'抢到了',
        ]);
       
    }
    public function init(){

        $redis = \libs\Redis::getInstance();
        //初始化数据库存量
        $redis->set('redbag_stock',20);
        //初始化空的集合
        $key = 'redbag_'.date('Ymd');
        $redis->sadd($key,'-1');
        //设置过期时间
        $redis->expire($key,3900);     
        
    }
    //监听消息队列
    public function makeOrder(){
        $redis = \libs\Redis::getInstance();
        $model = new \models\Red;

        // 设置 socket 永不超时
        ini_set('default_socket_timeout', -1); 

        echo "开始监听红包队列... \r\n";

        while(true){
            $data = $redis->brpop('redbag_orders',0);

            $userId = $daa[1];
            //下订单
            $model->create($userId);

            echo" =======有人抢到红包了";
        }
    } 
}   