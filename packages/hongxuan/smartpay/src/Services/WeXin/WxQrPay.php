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

    /**
     * 支付
     * @return array|false|mixed
     * @throws PaymentException
     */
    public function pay()
    {
        $this->setSign(self::TRADE_TYPE_WX_QR);
        $data = $this->retData;
        $xml = SomeUtils::toXml($data);

        $result = $this->sendReq($xml, self::PAY_URL, 'POST');
        $result['out_trade_no'] = array_get($this->config, 'order.out_trade_no');
        $result['total_amount'] = array_get($this->config, 'order.total_amount');

//        // 扫码支付，返回链接
//        return $result['code_url'];
        return $result;
    }

}