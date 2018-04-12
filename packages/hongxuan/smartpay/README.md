# smartpay
用于Laravel的第三方支付工具包，使用IOC容器技术，支持composer加载。整合支付宝、微信等支付方式

# 使用例子
```php

    // 支付宝网页扫码支付
    $result = Payment::driver('alipay')
        ->setPayType('ali_web') // 可不设置此参数，默认为“ali_web”（网页扫码）支付方式
        ->setOrder([
            'body'         => '商品描述',
            'subject'      => '订单名称',
            'total_amount' => 0.01,   // 支付金额
            'out_trade_no' => '1010', // 商户订单号
        ])
        ->setNotifyUrl('http://www.baidu.com') // 异步通知地址，公网可以访问
        ->setReturnUrl('http://www.baidu.com') // 同步跳转地址，公网可访问
        ->pay();

```