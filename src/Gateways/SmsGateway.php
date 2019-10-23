<?php

namespace App\Gateways;

use App\Traits\ResultTrait;
use Yuanyou\Risk\Contracts\MessageInterface;
use Yuanyou\Risk\Contracts\PhoneNumberInterface;
use Yuanyou\Risk\Gateways\Gateway;
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