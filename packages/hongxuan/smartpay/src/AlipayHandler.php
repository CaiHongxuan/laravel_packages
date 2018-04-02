<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/10 0010
 * Time: 14:42
 */

namespace Hongxuan\Smartpay;


use Hongxuan\Smartpay\Services\Alipay\AliWapPay;
use Hongxuan\Smartpay\Services\Alipay\AliWebPay;

class AlipayHandler extends PaymentHandlerAbstract
{
    /**
     * 支持的支付方式
     */
    const ALI_WEB = 'ali_web'; // 支付宝电脑网站支付
    const ALI_WAP = 'ali_wap'; // 支付宝手机网站支付

    public static $pay_type = [
        self::ALI_WEB,
        self::ALI_WAP
    ];

    /**
     * 获取配置信息
     * @param $config
     * @return mixed
     * @throws PaymentException
     */
    protected function getConfig($config)
    {
        if(!isset($config['app_id'])||trim($config['app_id'])==""){
            throw new PaymentException("app_id should not be NULL!");
        }
        if(!isset($config['mpk'])||trim($config['mpk'])==""){
            throw new PaymentException("private_key should not be NULL!");
        }
        if(!isset($config['alipay_public_key'])||trim($config['alipay_public_key'])==""){
            throw new PaymentException("alipay_public_key should not be NULL!");
        }
        if(!isset($config['charset'])||trim($config['charset'])==""){
            throw new PaymentException("charset should not be NULL!");
        }
        if(!isset($config['sign_type'])||trim($config['sign_type'])==""){
            throw new PaymentException("sign_type should not be NULL!");
        }
        if(!isset($config['gatewayUrl'])||trim($config['gatewayUrl'])==""){
            throw new PaymentException("gateway_url should not be NULL!");
        }

        return $config;
    }

    /**
     * 手机、电脑网站支付
     * @throws PaymentException
     */
    function pay()
    {
        // 获取支付方式，默认为：ali_web
        $pay_type = array_get($this->config, 'pay_type', self::ALI_WEB);
        if (!in_array($pay_type, self::$pay_type)) {
            throw new PaymentException('Unsupported payment methods');
        }

        switch ($pay_type) {
            case self::ALI_WEB:
                AliWebPay::pay($this->config);
                break;
            case self::ALI_WAP:
                AliWapPay::pay($this->config);
                break;
            default :
                AliWebPay::pay($this->config);
        }
    }

    /**
     * 订单查询
     *
     * @return mixed
     */
    function tradeQuery()
    {
        // TODO: Implement tradeQuery() method.
    }

    /**
     * 订单退款
     *
     * @return mixed
     */
    function refund()
    {
        // TODO: Implement refund() method.
    }

    /**
     * 订单退款查询
     *
     * @return mixed
     */
    function refundQuery()
    {
        // TODO: Implement refundQuery() method.
    }

    /**
     * 账单下载
     *
     * @return mixed
     */
    function download()
    {
        // TODO: Implement download() method.
    }
}