<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/28 0028
 * Time: 17:09
 */

namespace App\Http\Controllers;



use Hongxuan\Smartpay\Facades\Payment;
use Hongxuan\Smartpay\PaymentException;

class IndexController extends Controller
{
    public function index()
    {

        try {
            // 支付宝网页扫码支付
            $result = Payment::driver('alipay')
                ->setPayType('ali_web')// 可不设置此参数，默认为“ali_web”（网页扫码）支付方式
                ->setOrder([
                    'body'         => '商品描述',
                    'subject'      => '订单名称',
                    'total_amount' => 0.01,   // 支付金额
                    'out_trade_no' => 'D1711130001', // 商户订单号
                ])
                ->setNotifyUrl('http://www.baidu.com')// 异步通知地址，公网可以访问
                ->setReturnUrl('http://www.baidu.com')// 同步跳转地址，公网可访问
                ->tradeQuery();
        } catch (PaymentException $e) {
            return $e->errorMsg();
        }

        dd($result);
    }

}