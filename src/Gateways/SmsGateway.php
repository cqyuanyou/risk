<?php

namespace App\Gateways;

use App\Traits\ResultTrait;
use Yuanyou\EasySms\Contracts\MessageInterface;
use Yuanyou\EasySms\Contracts\PhoneNumberInterface;
use Yuanyou\EasySms\Gateways\Gateway;
use Yuanyou\EasySms\Support\Config;
use Yuanyou\EasySms\Traits\HasHttpRequest;

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
     * @param \Yuanyou\EasySms\Contracts\PhoneNumberInterface $to
     * @param \Yuanyou\EasySms\Contracts\MessageInterface $message
     * @param \Yuanyou\EasySms\Support\Config $config
     *
     * @return array
     */
    public function send(PhoneNumberInterface $to, MessageInterface $message, Config $config)
    {
        $params = [
            'api_token' => $config->get('token'),
            'sign' => strtoupper(md5($config->get('secret_key') . $to->getNumber())),
            'content' => $message->getContent(),
            'template_id' => $message->getTemplate(),
            'params' => $message->getData(),
            'sign_id' => $config->get('sign_id'),
            'mobile' => $to->getNumber()
        ];
        return $this->post(self::ENDPOINT_URI, $params);
    }

    protected function getBaseUri()
    {
        return self::ENDPOINT_HOST;
    }
}