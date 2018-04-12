<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/29 0029
 * Time: 17:09
 */

namespace Hongxuan\Smartpay\Services\Alipay;
use Hongxuan\Smartpay\AlipayHandler;

/**
 * 支付宝手机网站支付
 * Class AliWapPay
 * @package Hongxuan\Smartpay\Services\Alipay
 */
class AliWapPay extends AlipayHandler
{

    /**
     * 支付
     * @return string
     */
    public function pay()
    {
        return 'wap pay';
    }

}