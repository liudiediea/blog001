<?php
namespace controller;
use Endroid\QrCode\QrCode;

class QrcodeController{
    
    public function qrcode()
    {
        //吧一个字符串生成一个二维码图片显示
        $str = $_GET['code'];
        $qrCode = new QrCode($str);
        header('Content-Type: '.$qrCode->getContentType());
        echo $qrCode->writeString();
    }
    }