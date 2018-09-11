<?php
namespace controller;

use Yansongda\Pay\Pay;

class AlipayController
{
    public $config = [
        'app_id' => '2016091700531265',
        // 通知地址
        'notify_url' => 'http://740930d3.ngrok.io/alipay/notify',
        // 跳回地址
        'return_url' => 'http://localhost:9999/alipay/return',
        // 支付宝公钥
        'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAxzwiqczlk2OQgpC4unBkB0tinoIqLmbUh8E/f0xUcoCzf2EMAVORUh0NV+nP3tAB7FnW0Vl40kt2NoglgyBp/tlzZrP0xv+GCqppaKK9kYx5QuEtk4rqGaywFctVO03FxwjdaJUKknMuCMfom5iwvtQDFUZ7R4tSYv0EoC7uoUmY7AQHGI1WuDhqSJ9lM+J6fITMh90+Ino4NjcsmQXu24piwP2AUItjmvUAiNQxjp8F3VUpYPMCazHbjMLXXMq8bvFMen7A/i05bpB3hV/Z2wvcdwJnlkWA1Sfawnfne5ZwRBJtRZNgGW97Xnl3ZMYs9fjlYdU3FVWv/00LWJikHwIDAQAB',
        // 商户应用密钥
        'private_key' => 'MIIEpQIBAAKCAQEA1wL1ScbLQ0Cay/yhL6vzL23o/WCMurunCwO4UG7MspFcDld4bvDpeAoXhIBr5nCwudcPc8l4IgVSL5aU2ZPV9U6Z2gNm80aijvFahL1SvSUCgEEk/p60kIj5mnZyye54U6WZjvarbFRka2ICo6NcdTFNxDylGc4YfzA9lOGlRvMZJ84rbp5z9TJBQAptnRNP3e6SSpbCwt/5dSHrxq/y3H3hqkwEzk9B2W+BDgv623BCvzv2oHvNzlds4UT0AGxd7rhm0GGuDSAKq/GcAEjIGuJjR3JjLMoXoWzO/iHXP411HHLrfI2rVU30nbRSfX8aEPer1qPZf052/QpXGf7eWwIDAQABAoIBAQDIe/ymB1R+6B2u+Wh+4BHI9u5aXI28jL9smNJjRX1fQAUbZlpo8AByqs5VAb4ayJcxIiRJbeGzr0x8DSfMXXoS23DABY4YZ5OqP8iMn1AoB/t2BchuFyP85YwAiFPukIroTDCMStt7DpH4QxJ87RWIPSnrQjUcg7z508yIJ4MKTGsot3aOEGdorQBhPJb8AbzX50MNpiQrmapnpuopKRLHBlSfhpUivc20nZWju6L04EjDclBzyYZ4fI+Ew6kX0a2abtmOMOfgkgPE8AE9Lpr1MAW+EpSu+4DCZTijf0qBVZW73xr8QV43he4sTJvyIirV9CZZ8EdMKPIb/jMRtxt5AoGBAPMkNRJd+td6fg0vK3xM5VikH/CiZasylAcIH7Q9ibkbyRTdEKFaxs8exPa/ZpNt2X9HhA8MfOkaAQxbkMzXKUrDLU7OJWZKKpZ7xEsosLW3zK61VyMI73TxWWFl5vzGRs8IYnDaRReBg051E3ywuTHiCzkyTT4/afdd7H53XUWVAoGBAOJh6XEu8KUmlAJFitFSWtzFtMvwd8PkpLeD9LlksqLviHeARl2eV9Jje5cma39c/GiSHDhwNQaNrME/Ba4nMYhkj6XEopB9J24VrUZ28vLFkdH0IQdNZd2m1rWM1eFYfIfIuuKa5GgazpE4TifGo7okoXe01CTvcylskWbfbLgvAoGBAI3SWc6L2OvlPC9Oi0vmwoQwv5yp7Smtx+BOIcgNeQ7rISA1TiUAASUA6pyOyjNGiO4P9fydKSMCkwf1RQHmouRD8mKcJf6DVlIOgVHhuylTu2VQyzHZXWtV9++y88gPk2/hkSIIfvKWDdQuwB4kKvHoyuki6SFc9d9e/rpJv7L9AoGBAI+FfadZGA3MCHfsONb+PEbNPRMyb+uMsHT8PpGu6qXr8Hu6omHF+m2Szo8EN5C2lfuB7kxFrwhpl4Woe+RuSrPq9TsmD95EylO7gHA1B0+svAb8nFfx1MwScvsEv7AvFjxLoVf61SW/IQjmRn5nK/PeN1QaG4kTGdLAVup+aYHXAoGAHDpPG69ph8gD9Jk5+XNv7EdPrn1VszPUcL7TluSCkkau/Nlipm8MGylv2HXLkD8CvPLgPe350K7ooYh/olZgCU7FlFAH2Wf20KKM1WA8dXZDBja+6KmIkuPlOpNDYpwPSiXCk/7u84vNSo57TRPhZZJU0qVlWfb9kdhW/jqU8PA=',
        // 沙箱模式（可选）
        'mode' => 'dev',
    ];
    // 发起支付
    public function pay()
    {
        //接收订单Binahao
        $sn = $_POST['sn'];
        //取出订单信息 比如：金额
        $order = new \models\Order;
        $data = $order->findBySn($sn);

        if( $data['status'] == 0){
            // 跳转到支付宝
            $alipay = Pay::alipay($this->config)->web([
                'out_trade_no' => $sn,
                'total_amount' => $data['money'],
                'subject' => '智聊系统用户充值 ：'.$data['money'].'元',
            ]);
            $alipay->send();
        }else{
            die('不能重复支付');
        }
        // var_dump($data);
        // die;

    }
    // 支付完成跳回
    public function return()
    {
        $data = Pay::alipay($this->config)->verify(); // 是的，验签就这么简单！
        echo '<h1>支付成功！</h1> <hr>';
        var_dump( $data->all() );
    }
    // 接收支付完成的通知
    public function notify()
    {
        $alipay = Pay::alipay($this->config);
        try{
            $data = $alipay->verify(); // 是的，验签就这么简单！
            // 这里需要对 trade_status 进行判断及其它逻辑进行判断，在支付宝的业务通知中，只有交易通知状态为 TRADE_SUCCESS 或 TRADE_FINISHED 时，支付宝才会认定为买家付款成功。
            if($data->trade_status == 'TRADE_SUCCESS' || $data->trade_status == 'TRADE_FINISHED')
            {
                // 更新订单状态
                $order = new \models\Order;
                // 获取订单信息
                $orderInfo = $order->findBySn($data->out_trade_no);
                
                // 如果订单的状态为未支付状态 ，说明是第一次收到消息，更新订单状态 
                if($orderInfo['status'] == 0)
                {
                    
                    //开启事务
                    $order->startTrans();
                    // 设置订单为已支付状态
                    $res1 = $order->setPaid($data->out_trade_no);
                    // 更新用户余额
                     $user = new \models\User;
                     $res2 = $user->addMoney($orderInfo['money'], $orderInfo['user_id']);
                }
                if($res1 && $res2){
                    $order->commit();
                }
                else{
                    $order->rollback();
                }
            }


        
        } catch (\Exception $e) {   
            die('违法操作');
        }
        // 返回响应
        $alipay->success()->send();
    }

    // 退款
    public function refund()
    {
        // 生成唯一退款订单号（以后使用这个订单号，可以到支付宝中查看退款的流程）
        $refundNo = md5( rand(1,99999) . microtime() );

        try{
            $order = [
                'out_trade_no' => '1536568778',    // 退款的本地订单号
                'refund_amount' => 0.01,              // 退款金额，单位元
                'out_request_no' => $refundNo,     // 生成 的退款订单号
            ];

            // 退款
            $ret = Pay::alipay($this->config)->refund($order);

            if($ret->code == 10000)
            {
                echo '退款成功！';
            }
            else
            {
                echo '失败' ;
                var_dump($ret);
            }
        }
        catch(\Exception $e)
        {
            var_dump( $e->getMessage() );
        }
    }
}