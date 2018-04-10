<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/29 0029
 * Time: 17:04
 */

namespace Hongxuan\Smartpay\Services\Alipay;
use GuzzleHttp\Client;
use Hongxuan\Smartpay\PaymentException;
use Hongxuan\Smartpay\Utils\Rsa2Encrypt;
use Hongxuan\Smartpay\Utils\RsaEncrypt;
use Hongxuan\Smartpay\Utils\SomeUtils;
use InvalidArgumentException;

/**
 * 支付宝电脑网站支付
 * Class AliWebPay
 * @package Hongxuan\Smartpay\Services\Alipay
 */
class AliWebPay
{

    // 这个类里面的一些方法需要做进一步的提取优化

    // web 支付接口名称
    protected static $method = 'alipay.trade.page.pay';

    protected static $config = [];

    protected static $retData = [];

    /**
     * 支付
     * @param $config
     * @return string
     */
    public static function pay($config)
    {
        self::$config = $config;

        self::setSign();
        $data = self::$retData;

        $sign = $data['sign'];
        unset($data['sign']);
        ksort($data);
        reset($data);

        // 支付宝新版本  需要转码
        foreach ($data as &$value) {
            $value = SomeUtils::transcode($value, array_get($config, 'charset'));
        }
        $data['sign'] = $sign; // sign  需要放在末尾

        header('Location: ' . array_get($config, 'gatewayUrl') . '?' . http_build_query($data));
        exit;
    }

    /**
     * 支付宝业务发送网络请求，并验证签名
     * @param array $data
     * @param string $method 网络请求的方法， get post 等
     * @return mixed
     * @throws PaymentException
     */
    protected function sendReq(array $data, $method = 'GET')
    {
        $client = new Client([
            'base_uri' => array_get(self::$config, 'gatewayUrl'),
            'timeout' => '10.0'
        ]);
        $method = strtoupper($method);
        $options = [];
        if ($method === 'GET') {
            $options = [
                'query' => $data,
                'http_errors' => false
            ];
        } elseif ($method === 'POST') {
            $options = [
                'form_params' => $data,
                'http_errors' => false
            ];
        }
        // 发起网络请求
        $response = $client->request($method, '', $options);
        if ($response->getStatusCode() != '200') {
            throw new PaymentException('网络发生错误，请稍后再试curl返回码：' . $response->getReasonPhrase());
        }
        $body = $response->getBody()->getContents();
        try {
            $body = \GuzzleHttp\json_decode($body, true);
        } catch (InvalidArgumentException $e) {
            throw new PaymentException('返回数据 json 解析失败');
        }
        $responseKey = str_ireplace('.', '_', self::$method) . '_response';
        if (! isset($body[$responseKey])) {
            throw new PaymentException('支付宝系统故障或非法请求');
        }
        // 验证签名，检查支付宝返回的数据
        $flag = $this->verifySign($body[$responseKey], $body['sign']);
        if (! $flag) {
            throw new PaymentException('支付宝返回数据被篡改。请检查网络是否安全！');
        }
        // 这里可能带来不兼容问题。原先会检查code ，不正确时会抛出异常，而不是直接返回
        return $body[$responseKey];
    }

    /**
     * 设置签名
     * @author helei
     */
    protected static function setSign()
    {
        self::buildData();
        $data = self::$retData;

        unset($data['sign']);

        ksort($data);
        reset($data);

        $signStr = SomeUtils::createLinkString($data);
        self::$retData['sign'] = self::makeSign($signStr);
    }

    /**
     * 构建 支付 加密数据
     * @author helei
     */
    protected static function buildData()
    {
        $bizContent = self::getBizContent();
        $bizContent = SomeUtils::paraFilter($bizContent);// 过滤掉空值，下面不用在检查是否为空
        $signData = [
            // 公共参数
            'app_id'        => array_get(self::$config, 'app_id'),
            'method'        => self::$method,
            'format'        => array_get(self::$config, 'format'),
            'charset'       => array_get(self::$config, 'charset'),
            'sign_type'     => array_get(self::$config, 'sign_type'),
            'timestamp'     => date('Y-m-d H:i:s'),
            'version'       => array_get(self::$config, 'version'),
            'notify_url'    => array_get(self::$config, 'notify_url'),
            // 业务参数
            'biz_content'   => json_encode($bizContent, JSON_UNESCAPED_UNICODE),
        ];
        // 电脑支付  wap支付添加额外参数
        if (in_array(self::$method, ['alipay.trade.page.pay', 'alipay.trade.wap.pay'])) {
            $signData['return_url'] = array_get(self::$config, 'return_url');
        }
        // 移除数组中的空值
        self::$retData = SomeUtils::paraFilter($signData);
    }

    /**
     * 业务请求参数的集合，最大长度不限，除公共参数外所有请求参数都必须放在这个参数中传递
     *
     * @return array
     */
    protected static function getBizContent()
    {
        $order = array_get(self::$config, 'order');
        $content = [
            'out_trade_no'  => strval(array_get($order, 'out_trade_no')),
            // 销售产品码，商家和支付宝签约的产品码，为固定值
            'product_code'  => 'FAST_INSTANT_TRADE_PAY',
            'total_amount'  => strval(array_get($order, 'total_amount')),
            'subject'       => strval(array_get($order, 'subject')),
            'body'          => strval(array_get($order, 'body')),
        ];

        return $content;
    }

    /**
     * 签名算法实现
     * @param string $signStr
     * @return string
     * @author helei
     */
    protected static function makeSign($signStr)
    {
        switch (array_get(self::$config, 'sign_type')) {
            case 'RSA':
                $rsa = new RsaEncrypt(SomeUtils::getRsaKeyValue(array_get(self::$config, 'mpk')));
                $sign = $rsa->encrypt($signStr);
                break;
            case 'RSA2':
                $rsa = new Rsa2Encrypt(SomeUtils::getRsaKeyValue(array_get(self::$config, 'mpk')));
                $sign = $rsa->encrypt($signStr);
                break;
            default:
                $sign = '';
        }
        return $sign;
    }

    /**
     * 检查支付宝数据 签名是否被篡改
     * @param array $data
     * @param string $sign  支付宝返回的签名结果
     * @return bool
     * @author helei
     */
    protected function verifySign(array $data, $sign)
    {
        $preStr = \GuzzleHttp\json_encode($data, JSON_UNESCAPED_UNICODE);// 主要是为了解决中文问题
        if (array_get(self::$config, 'sign_type') === 'RSA') {// 使用RSA
            $rsa = new RsaEncrypt(SomeUtils::getRsaKeyValue(array_get(self::$config, 'alipay_public_key'), 'public'));
            return $rsa->rsaVerify($preStr, $sign);
        } elseif (array_get(self::$config, 'sign_type') === 'RSA2') {// 使用rsa2方式
            $rsa = new Rsa2Encrypt(SomeUtils::getRsaKeyValue(array_get(self::$config, 'alipay_public_key'), 'public'));
            return $rsa->rsaVerify($preStr, $sign);
        } else {
            return false;
        }
    }

}