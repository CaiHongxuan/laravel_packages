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
            // 微信扫码支付
            $result = Payment::driver('weixin')
                ->setPayType('wx_qr')// 可不设置此参数，默认为“wx_qr”（扫码）支付方式
                ->setOrder([
                    'body'           => '商品描述',
//                    'subject'        => '订单名称',
                    'total_amount'   => 0.01,   // 支付金额
//                    'refund_amount'  => 0.02,   // 退款金额
//                    'trade_no'       => '', // 支付宝交易号
                    'out_trade_no'   => '0101', // 商户订单号
                    'product_id'     => '0101', // 商品ID
                    'attach'         => 'test', //
                    'time_start'     => date('YmdHis'), // 订单生成时间
//                    'time_expire'    => date('YmdHis', time() + 600), // 订单失效时间
                    'goods_tag'      => 'test', // 商品标记
//                    'time_express'   => '1m', // 超时时间
//                    'out_request_no' => 'D1711160014', // 标识一次退款请求，同一笔交易多次退款需要保证唯一，如需部分退款，则此参数必传
//                    'refund_reason'  => '不要了', // 退款原因
//                    'bill_type'      => 'signcustomer',
//                    'bill_date'      => date('Y-m', strtotime('2017-09-16 10:10:10'))
                ])
                ->setNotifyUrl('http://www.baidu.com')// 异步通知地址，公网可以访问
//                ->setReturnUrl('http://www.baidu.com')// 同步跳转地址，公网可访问
                ->pay();
        } catch (PaymentException $e) {
            return $e->errorMsg();
        }

        dd($result);
    }

}