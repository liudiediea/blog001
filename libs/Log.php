<?php
namespace libs;

class Log{
    private $fp;
    //參數 日志文件名
    public function __construct($fileName){
        $date = date('Y-m-d H:i:s');
        //执行日志内容格式
        $c = $date. "\r\n";
        $c .= str_repeat('=', 120) . "\r\n"; 
        $c .= $content . "\r\n\r\n";
        fwrite($this->fp,$c);
        

    }

    //想日志文件中追加内容
    public function log($content){
        fwrite($this->fp,$content."\r\n");
    }
}