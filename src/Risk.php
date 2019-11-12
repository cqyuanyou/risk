<?php

/*
 * This file is part of the yuanyou/easy-sms.
 *
 * (c) yuanyou <wuwanhui@yeah.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Yuanyou\Risk;

use Exception;
use Yuanyou\Risk\Support\Config;
use Yuanyou\Risk\Traits\HasHttpRequest;

class Risk
{

    use HasHttpRequest;

    const ENDPOINT_HOST = 'http://risk.4255.cn/';

    /**
     * @var \Yuanyou\Risk\Supports\Config
     */
    protected $config;


    /**
     * @var array
     */
    protected $strategies = [];


    public function __construct()
    {
        $config = [
            'url' => getenv('RISK_URL'),
            'token' => getenv('RISK_TOKEN'),
            'secret_key' => getenv('RISK_SECRET_KTY'),
            'sign_id' => getenv('RISK_SMS_SIGN_ID'),
        ];
        $this->setConfig($config);

    }

    public function setConfig(array $config)
    {
        $this->config = new  Config($config);

    }

    /**
     * 短信发送
     */
    public function MessageSend($mobiles, $templateId, Array $params, $signId = 0)
    {
        $ENDPOINT_URI = '/api/merchant/sms/record/send';
        $params = [
            'api_token' => $this->config->get('token'),
            'template_id' => $templateId,
            'params' => json_encode($params),
            'mobile' => $mobiles,
            'sign_id' => $signId,
            'timestamp' => time()
        ];
        $params['sign'] = $this->getSign($params, $this->config->get('secret_key'));
        $result = $this->post($this->config->get('url') . $ENDPOINT_URI, $params);

        return $result;
    }


    /**
     * 银行卡四要素认证
     */
    public function CertificationBankFour($name, $mobile, $idCard, $cardNo)
    {
        $ENDPOINT_URI = '/api/merchant/certification/bank/four';
        $params = [
            'api_token' => $this->config->get('token'),
            'name' => $name,
            'mobile' => $mobile,
            'id_card' => $idCard,
            'card_no' => $cardNo,
            'timestamp' => time()
        ];
        $params['sign'] = $this->getSign($params, $this->config->get('secret_key'));
//        return $params;
        $result = $this->post($this->config->get('url') . $ENDPOINT_URI, $params);

        return $result;
    }


    /**
     * 手机号三要素认证
     */
    public function CertificationMobileThree($name, $mobile, $idCard)
    {
        $ENDPOINT_URI = '/api/merchant/certification/mobile/three';
        $params = [
            'api_token' => $this->config->get('token'),
            'name' => $name,
            'mobile' => $mobile,
            'id_card' => $idCard,
            'timestamp' => time()
        ];
        $params['sign'] = $this->getSign($params, $this->config->get('secret_key'));
        $result = $this->post($this->config->get('url') . $ENDPOINT_URI, $params);

        return $result;
    }


    /**
     * 签名生成
     *
     * @param array $params
     *
     * @return string
     */
    protected function getSign(array $params, $secretKey)
    {
        //参数中含有sign

        if (isset($params['sign'])) {
            unset($params['sign']);
        }
        if (isset($params['secret_key'])) {
            unset($params['secret_key']);
        }
        if (isset($params['url'])) {
            unset($params['url']);
        }
        if (isset($params['api_token'])) {
            unset($params['api_token']);
        }
        if (isset($params['method'])) {
            unset($params['method']);
        }

        if (count($params) >= 1) {
            //参数字典排序
            ksort($params);
            $str = '';
            foreach ($params as $k => $v) {
                if (strlen($v) > 0) {
                    $str .= $k . '=' . $v . '&';
                }

            }
            $str = substr($str, 0, strlen($str) - 1);
            $sign = md5($str . $secretKey);
            return $sign;
        }

        return false;
    }


}
