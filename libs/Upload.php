<?php
/*这个项目中 无论使用多少次 只生成一个对象

    单例：三私一公
*/
namespace libs;
class Upload{
    private function __construct(){} //不允许被new生成对象
    private function __clone(){}  //不允许被克隆
    private static $_obj = null;
    //提供一个公开的方法来获取对象
    public static function getInstance(){
        if(self::$_obj === null){
            self::$_obj = new self;
        }
        return self::$_obj;
    }

    private $_root = ROOT.'public/uploads/';
    private $_ext = ['image/jpeg','image/jpg','image/ejpeg','image/png','image/bmp','image/gif'];
    private $_maxsize = 1024*1024*1.5; //允许上传的最大尺寸

    private $_file;
    private $_subdir;
    public function upload($name,$subdir){

        $this->_file = $_FILES[$name];
        $this->_subdir = $subdir;
        
        if(!$this->_checkType()){
            die("图片类型不正确");
        }
        if(!$this->_checkSize()){
            die("图片尺寸不对");
        }
        
        $dir = $this->_makedir();
        $name = $this->_makename();

        move_uploaded_file($this->_file['tmp_name'], $this->_root.$dir.$name);
        //返回二级目录开始的目录  根目录不能返回
        return $dir.$name;


    }
    //创建目录
    private function _makedir(){
        $dir = $this->_subdir . '/' . date('Ymd');
        if(!is_dir($this->_root . $dir))
            mkdir($this->_root . $dir, 0777, TRUE);

            return $dir.'/';
        
    }
    //生成唯一名字
    private function _makename(){
        //2.生成唯一的文件名
        $name = md5(time() . rand(1,9999));
        //补上文件后缀  (字符串截取)
        $ext = strrchr($this->_file['name'],'.');
        return $name.$ext;
    }
    private function _checkType(){
        return in_array($this->_file['type'],$this->_ext);
    }
    private function _checkSize(){
        return $this->_file['size'] < $this->_maxsize;
    }
}