<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/29 0029
 * Time: 17:04
 */

namespace Hongxuan\Smartpay\Services\Alipay;
use Hongxuan\Smartpay\Utils\SomeUtils;

/**
 * 支付宝电脑网站支付
 * Class AliWebPay
 * @package Hongxuan\Smartpay\Services\Alipay
 */
class AliWebPay
{

    // web 支付接口名称
    protected $method = 'alipay.trade.page.pay';

    /**
     * 支付
     * @param $config
     * @return string
     */
    public static function pay($config)
    {

        $sign = $data['sign'];
        $data = ArrayUtil::removeKeys($data, ['sign']);
        $data = ArrayUtil::arraySort($data);
        // 支付宝新版本  需要转码
        foreach ($data as &$value) {
            $value = SomeUtils::transcode($value, $config['charset']);
        }
        $data['sign'] = $sign;// sign  需要放在末尾
        return $data;
        dd($config['gatewayUrl'] . '?' . http_build_query($config['order']));
        dd($config);

//        //设置订单信息
//        $payData = [
//            'product_code' => 'FAST_INSTANT_TRADE_PAY',
//            'out_trade_no' => '1',
//            'subject' => 'subject',
////            'total_amount' => 'total',
//            'total_amount' => 0.01,
//            'body' => 'body',
//        ];
//        $bizContent = json_encode($payData, JSON_UNESCAPED_UNICODE);
////        dd(dirname(__FILE__) . '/alipay.trade.page.pay-PHP-UTF-8/aop/AopClient.php');
//
//        require_once dirname(__FILE__) . '/alipay.trade.page.pay-PHP-UTF-8/aop/AopClient.php';
//        require_once dirname(__FILE__) . '/alipay.trade.page.pay-PHP-UTF-8/pagepay/buildermodel/AlipayTradePagePayContentBuilder.php';
//
//        //构造参数
//        $aop = new AopClient();
//        $aop->gatewayUrl = array_get($this->config, 'alipay_gateway_new');
//        $aop->appId = array_get($this->config, 'app_id');
//        $aop->rsaPrivateKey = array_get($this->config, 'app_private_key');
//        $aop->apiVersion = array_get($this->config, 'version', '1.0');
//        $aop->signType = array_get($this->config, 'sign_type');
//        $aop->postCharset = array_get($this->config, 'charset');
//        $aop->format = array_get($this->config, 'format', 'json');
//        $request = new AlipayTradePagePayRequest();
//        $request->setReturnUrl(array_get($this->config, 'return'));
//        $request->setNotifyUrl(array_get($this->config, 'notify'));
//        $request->setBizContent($bizContent);
//
//        //请求
//        $result = $aop->pageExecute($request);
//        //输出
//        echo $result;
        return 'web pay';
    }

}