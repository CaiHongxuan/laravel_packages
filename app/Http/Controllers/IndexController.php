<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/28 0028
 * Time: 17:09
 */

namespace App\Http\Controllers;



use Hongxuan\Smartpay\Facades\Payment;

class IndexController extends Controller
{
    public function index()
    {

        // 支付宝网页扫码支付
        $result = Payment::driver('alipay')
            ->setPayType('ali_web')
            ->setOrder([
                'body' => '商品描述',
                'subject' => '订单名称',
                'total_amount' => 0.01,
                'out_trade_no' => '1010', // 商户订单号
            ])
            ->setNotifyUrl('http://www.baidu.com')
            ->setReturnUrl('http://www.baidu.com')
            ->pay();

        dd($result);
//        Payment::pay();
//        $payment = new Payment();
//        dd($payment->driver());
    }

}