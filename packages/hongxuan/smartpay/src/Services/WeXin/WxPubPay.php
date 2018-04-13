<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/29 0029
 * Time: 17:16
 */

namespace Hongxuan\Smartpay\Services\WeXin;
use Hongxuan\Smartpay\PaymentException;
use Hongxuan\Smartpay\WeXinHandler;

/**
 * 微信公众号支付
 * Class WxPubPay
 * @package Hongxuan\Smartpay\Services\WeXin
 */
class WxPubPay extends WeXinHandler
{

    public function pay()
    {
        // 公众号支付,必须设置openid
        $openid = $this->openid;
        if (empty($openid)) {
            throw new PaymentException('用户在商户appid下的唯一标识,公众号支付,必须设置该参数.');
        }

        $subMchId = $this->sub_mch_id;// 如果是服务商模式，则 sub_openid 必须提供
        $subOpenid = $this->sub_openid;
        if ($subMchId && empty($subOpenid)) {
            throw new PaymentException('公众号的服务商模式，必须提供 sub_openid 参数.');
        }

        return '';
    }

}