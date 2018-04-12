<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/10 0010
 * Time: 14:42
 */

namespace Hongxuan\Smartpay;


use GuzzleHttp\Client;
use Hongxuan\Smartpay\Services\Alipay\AliWapPay;
use Hongxuan\Smartpay\Services\Alipay\AliWebPay;
use Hongxuan\Smartpay\Utils\Rsa2Encrypt;
use Hongxuan\Smartpay\Utils\RsaEncrypt;
use Hongxuan\Smartpay\Utils\SomeUtils;
use InvalidArgumentException;

class AlipayHandler extends PaymentHandlerAbstract
{

    /**
     * @var array 配置信息
     */
    protected $config;

    protected $retData = [];

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
     * 支付宝API接口名称
     */
    // web 支付接口名称
    const API_METHOD_NAME_WEB_PAY = 'alipay.trade.page.pay';
    // wap 支付接口名称
    const API_METHOD_NAME_WAP_PAY = 'alipay.trade.wap.pay';
    // 交易查询接口名称
    const API_METHOD_NAME_QUERY = 'alipay.trade.query';
    // 交易退款接口名称
    const API_METHOD_NAME_REFUND = 'alipay.trade.refund';
    // 交易退款查询接口名称
    const API_METHOD_NAME_REFUND_QUERY = 'alipay.trade.fastpay.refund.query';
    // 交易关闭接口名称
    const API_METHOD_NAME_CLOSE = 'alipay.trade.close';
    // 对账单下载接口名称
    const API_METHOD_NAME_DOWNLOAD = 'alipay.data.dataservice.bill.downloadurl.query';

    public function __construct(array $config = [])
    {
        parent::__construct($config);

        $this->config = $config;
    }

    /**
     * 获取配置信息
     * @param $config
     * @return mixed
     * @throws PaymentException
     */
    protected function getConfig($config)
    {
        if (!isset($config['app_id']) || trim($config['app_id']) == "") {
            throw new PaymentException("app_id should not be NULL!");
        }
        if (!isset($config['mpk']) || trim($config['mpk']) == "") {
            throw new PaymentException("private_key should not be NULL!");
        }
        if (!isset($config['alipay_public_key']) || trim($config['alipay_public_key']) == "") {
            throw new PaymentException("alipay_public_key should not be NULL!");
        }
        if (!isset($config['charset']) || trim($config['charset']) == "") {
            throw new PaymentException("charset should not be NULL!");
        }
        if (!isset($config['sign_type']) || trim($config['sign_type']) == "") {
            throw new PaymentException("sign_type should not be NULL!");
        }
        if (!isset($config['gatewayUrl']) || trim($config['gatewayUrl']) == "") {
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
                (new AliWebPay($this->config))->pay();
                break;
            case self::ALI_WAP:
                AliWapPay::pay($this->config);
                break;
            default :
                (new AliWebPay($this->config))->pay();
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



    /**
     * 支付宝业务发送网络请求，并验证签名
     * @param array $data
     * @param string $method_name 支付宝API接口名称
     * @param string $method 网络请求的方法， get post 等
     * @return mixed
     * @throws PaymentException
     */
    protected function sendReq(array $data, $method_name = '', $method = 'GET')
    {
        $client = new Client([
            'base_uri' => array_get($this->config, 'gatewayUrl'),
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
        $responseKey = str_ireplace('.', '_', $method_name) . '_response';
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
     * @param $method_name [支付宝API接口名称]
     */
    protected function setSign($method_name)
    {
        $this->buildData($method_name);
        $data = $this->retData;

        unset($data['sign']);

        ksort($data);
        reset($data);

        $signStr = SomeUtils::createLinkString($data);
        $this->retData['sign'] = $this->makeSign($signStr);
    }

    /**
     * 构建 支付 加密数据
     * @param $method_name [支付宝API接口名称]
     */
    protected function buildData($method_name)
    {
        $bizContent = $this->getBizContent();
        $bizContent = SomeUtils::paraFilter($bizContent);// 过滤掉空值，下面不用在检查是否为空
        $signData = [
            // 公共参数
            'app_id'        => array_get($this->config, 'app_id'),
            'method'        => $method_name,
            'format'        => array_get($this->config, 'format'),
            'charset'       => array_get($this->config, 'charset'),
            'sign_type'     => array_get($this->config, 'sign_type'),
            'timestamp'     => date('Y-m-d H:i:s'),
            'version'       => array_get($this->config, 'version'),
            'notify_url'    => array_get($this->config, 'notify_url'),
            // 业务参数
            'biz_content'   => json_encode($bizContent, JSON_UNESCAPED_UNICODE),
        ];
        // 电脑支付  wap支付添加额外参数
        if (in_array($method_name, ['alipay.trade.page.pay', 'alipay.trade.wap.pay'])) {
            $signData['return_url'] = array_get($this->config, 'return_url');
        }
        // 移除数组中的空值
        $this->retData = SomeUtils::paraFilter($signData);
    }

    /**
     * 业务请求参数的集合，最大长度不限，除公共参数外所有请求参数都必须放在这个参数中传递
     *
     * @return array
     */
    protected function getBizContent()
    {
        $order = array_get($this->config, 'order');
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
     */
    protected function makeSign($signStr)
    {
        switch (array_get($this->config, 'sign_type')) {
            case 'RSA':
                $rsa = new RsaEncrypt(SomeUtils::getRsaKeyValue(array_get($this->config, 'mpk')));
                $sign = $rsa->encrypt($signStr);
                break;
            case 'RSA2':
                $rsa = new Rsa2Encrypt(SomeUtils::getRsaKeyValue(array_get($this->config, 'mpk')));
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
     */
    protected function verifySign(array $data, $sign)
    {
        $preStr = \GuzzleHttp\json_encode($data, JSON_UNESCAPED_UNICODE);// 主要是为了解决中文问题
        if (array_get($this->config, 'sign_type') === 'RSA') {// 使用RSA
            $rsa = new RsaEncrypt(SomeUtils::getRsaKeyValue(array_get($this->config, 'alipay_public_key'), 'public'));
            return $rsa->rsaVerify($preStr, $sign);
        } elseif (array_get($this->config, 'sign_type') === 'RSA2') {// 使用rsa2方式
            $rsa = new Rsa2Encrypt(SomeUtils::getRsaKeyValue(array_get($this->config, 'alipay_public_key'), 'public'));
            return $rsa->rsaVerify($preStr, $sign);
        } else {
            return false;
        }
    }
}