<?php

namespace Yuanyou\Risk\Gateways;

use Yuanyou\Risk\Contracts\MessageInterface;
use Yuanyou\Risk\Contracts\PhoneNumberInterface;
use Yuanyou\Risk\Exceptions\GatewayErrorException;
use Yuanyou\Risk\Support\Config;
use Yuanyou\Risk\Traits\HasHttpRequest;

class SmsGateway extends Gateway
{
    use HasHttpRequest;

    const ENDPOINT_HOST = 'http://risk.4255.cn/';

    const ENDPOINT_URI = '/api/merchant/sms/record/send';

    protected $api_token;

    protected $secret_key;

    protected $sign = null;

    protected $client;


    /**
     * Send a short message.
     *
     * @param \Yuanyou\Risk\Contracts\PhoneNumberInterface $to
     * @param \Yuanyou\Risk\Contracts\MessageInterface $message
     * @param \Yuanyou\Risk\Support\Config $config
     *
     * @return array
     * @throws GatewayErrorException
     */
    public function send(PhoneNumberInterface $to, MessageInterface $message, Config $config)
    {
        $params = [
            'api_token' => $config->get('token'),
            'content' => $message->getContent(),
            'template_id' => $message->getTemplate(),
            'params' => json_encode($message->getData()),
            'sign_id' => $config->get('sign_id'),
            'mobile' => $to->getNumber(),
            'timestamp' => time()
        ];
        $params['sign'] = $this->getSign($params, $config->get('secret_key'));
        $result = $this->post(self::ENDPOINT_URI, $params);

        if (isset($result['status']) && $result['status'] == false) {
            throw new GatewayErrorException($result['msg'], $result['code'], $result);
        }

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

    protected function getBaseUri()
    {
        return self::ENDPOINT_HOST;
    }
}