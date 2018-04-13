<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/29 0029
 * Time: 17:18
 */

namespace Hongxuan\Smartpay\Services\WeXin;

use Hongxuan\Smartpay\PaymentException;
use Hongxuan\Smartpay\Utils\SomeUtils;
use Hongxuan\Smartpay\WeXinHandler;

/**
 * 微信扫码支付
 * Class WxQrPay
 * @package Hongxuan\Smartpay\Services\WeXin
 */
class WxQrPay extends WeXinHandler
{

    public function pay()
    {
        $order = array_get($this->config, 'order');
        // 检查订单号是否合法
        if (!isset($order['out_trade_no']) || empty($order['out_trade_no']) || mb_strlen($order['out_trade_no']) > 64) {
            throw new PaymentException('订单号不能为空，并且长度不能超过64位');
        }
        // 检查金额不能低于0.01
        if (!isset($order['total_amount']) || bccomp($order['total_amount'], 0.01, 2) === -1) {
            throw new PaymentException('支付金额不能低于 0.01 元');
        }
        // 检查 商品名称
        if (!isset($order['body']) || empty($order['body'])) {
            throw new PaymentException('必须提供商品名称');
        }

        $this->setSign(self::TRADE_TYPE_WX_QR);
        $data = $this->retData;
        $xml = SomeUtils::toXml($data);
//        dd($data);

        $result = $this->sendReq($xml, self::QR_URL, 'POST');
        $result['out_trade_no'] = $order['out_trade_no'];
        $result['total_amount'] = $order['total_amount'];

//        // 扫码支付，返回链接
//        return $result['code_url'];
        return $result;
    }

}