<?php

return [

    /*
    |--------------------------------------------------------
    | Default Payment Driver
    |--------------------------------------------------------
    |
    | Supported: "alipay", "weixin"
    |
    */

    'default' => env('PAYMENT_DRIVER', 'weixin'),

    /*
    |--------------------------------------------------------
    | Payment Drivers
    |--------------------------------------------------------
    |
    | Here you may configure the driver information for each
    | driver that is used by your application. A default
    | configuration has been added. You are free to add more.
    |
    */

    'drivers' => [

        'alipay' => [
            'driver'  => 'alipay',

            //应用ID,您的APPID。
            'app_id' => "2017080408028984",

            //支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
            'alipay_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAn7w5W2s260OfTdzraolo6qNP6CEMJV3eSe5czGVqMJ6I3Fy12hm9US9qBsfWh7oYGpllXQc4rTcbKTrZdJKPFuv60NGkH6VoL0Qlcaggi0IGpuCDD5QPLfGQ3Pj6+Y8E8Lom5QRL4eNj59buht2y5zgysGIjTHuT31MtROAvKH2nnaglZ/V7kPGsEvFLtoY89VTlViJOh8d4wUXhoN0A8eIr+fcPA/xS1AwQqqyqjNAGI8KoiFpR/mrz2Ju2vgLSxT69ao/zGfaqZj1XQo0I+hUL0xCRApIIOCPopu8+ZMybPjPzHQTAsJL/X1X2W33RR1Ex7E9tDv36QSM0lOwJuQIDAQAB',

            //商户私钥
            'private_key' => 'MIIEpQIBAAKCAQEA3fwPL0PJMyTWqa+fpKAfvN6uAI0OkW7MsIzTAM2JMZ48bnE0NOooT9IZ06QVhr0iibDU5Iw13Bdt5rmIQU+0aN9n8E64Er8ftqp+hOMHC+/7VwPU+WxBwOY7T1imjNF3UjvRML7I/A5TZT97zUOM9dmIxOIX1f7w2qilcIuTxpdflTb1WFxHKEIkYYCjfoo5nQg6tLXxYHvHyXiQXB3UDsUerkXrJI+F1B8xPw/obHzMdW4DZ7cyo9OhqNcwduHGO/q8uP+Nu93N5bECiubigb5jgnUhz38XpBUn17H8mffIIpwSFusv75tifviEoKI5mnI15Hx1fPX7FX0o6shdYQIDAQABAoIBAQCg20Adnd80QmOTPoJOhwG4mRw5pf2CgWmuHb3g/Q+HdwSPe1S7a1qezL6OUH6QzokygYMjwj5dKFUpNhR4T0uKGyl0R3a3jutqMI3RubmnetUErvArdbkIEU21J6Y4sKjoXBQwYG+/xpnD6obJrUN9+45SLQvctArQSBjqPxpscnZC8PXHQsrH8Pe6KJJkcskezzHv4SzOt9GLEbu9XZR5DoVZOV7tddrWtcSKvb3OuMhKA6HTNK3gWjecakSKYC9TWC9L7XQimKQ5Rmlhj8zCZQu6HpWofNFS4tngEFVy3wuvEsHKIVefptqUEpPeuOXvepTJ2LvyUrkJUhfX5PIBAoGBAP6SZptEYu4Rp2UNWpz44vx5qhO/mJfOhmPIPVvWc+HCWxg9Kg0roZnMYVu/XvMYAYzuZvBcWboTuSLdF2lZWuYeuLv8f4cAJd7ranrPdiaqf3guN47LXWJcGJkzcqbASrvj4MYrv8zuQaBtNUgt4RJ+fsDmFDeqXYiduj+oVobxAoGBAN8629zBhue4R+2H6xel3zpem97hFoLJjc2lKeAZac9U4gHULzJtjm5fQpvTy0LzyBHVFYWnreSbUMHiTdj3L1gnp09+FWZUfrPPjpEXJlo3QOnCbwSk9psYYDFu25GPmzk2fg35yyIj3peboyxQA2ZZipNtReTGXbFlxa+7JZ1xAoGBAL34bk1ryQ+zaOGGB5qgOHMEL6ExFyQh4DPSF8fSzwMn0GbULe9KIfvtgrG+q5Jo1a9fsL2pjOPJGB0mM/RP0/9p6Z2PHXOW7qvdrcYbzyWnkhwTES6kH/nolAqvU92QHbT8pp37w9Of8KVRGbPVWOI+N0Sn7Wpk3gu2+GfMrVVhAoGBAJqtaxk9E+BOJbDmJDUfn10Pn0vBhdqcFGDxV+HLWjDqrSv9PbLgjPfXlAzrpYU/7FrG3oHdHTYxlLSzvaNgK/MWju0a/XMJiz3GzQ+mDdInRRh0vH5oW+Q98LFwEj57VmA/bPr8IhAG8L72fgs/aguqccYTyoFqHhPE5EUPFVJRAoGAeYA1AKKeeqLr/AhG6p0d/NeUteb75X/rLi2/jlIYka+HH1XrM9B+M3obLU3S2Q0G79nH/lsSOCmfDvssPiVa8h1qKMj7jgHV49zKJ14nQJ6wB8j8ZSu9FglmcQQdtB5NbgrfL5qmdJ1suvjHplhOGHnUY4U+1GbAHHSd8DbHbBQ=',

            //异步通知地址
            'notify_url' => "http://外网可访问网关地址/alipay.trade.page.pay-PHP-UTF-8/notify_url.php",

            //同步跳转
            'return_url' => "http://外网可访问网关地址/alipay.trade.page.pay-PHP-UTF-8/return_url.php",

            //编码格式
            'charset' => "UTF-8",

            //调用的接口版本，固定为：1.0
            'version' => '1.0',

            //仅支持JSON
            'format' => 'JSON',

            //签名方式
            'sign_type' => "RSA2",

            //支付宝网关
            'gatewayUrl' => "https://openapi.alipay.com/gateway.do",
        ],

        'weixin' => [
            'driver'  => 'weixin',
            'app_id'  => 'your-app-id',
            'app_key'  => 'your-app-key'
        ],

    ],

];