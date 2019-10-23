<?php

/*
 * This file is part of the yuanyou/easy-sms.
 *
 * (c) yuanyou <wuwanhui@yeah.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Yuanyou\Risk\Gateways;

use Yuanyou\Risk\Contracts\MessageInterface;
use Yuanyou\Risk\Contracts\PhoneNumberInterface;
use Yuanyou\Risk\Exceptions\GatewayErrorException;
use Yuanyou\Risk\Support\Config;
use Yuanyou\Risk\Traits\HasHttpRequest;

/**
 * Class SendcloudGateway.
 *
 * @see http://sendcloud.sohu.com/doc/sms/
 */
class SendcloudGateway extends Gateway
{
    use HasHttpRequest;

    const ENDPOINT_TEMPLATE = 'http://www.sendcloud.net/smsapi/%s';

    /**
     * Send a short message.
     *
     * @param \Yuanyou\Risk\Contracts\PhoneNumberInterface $to
     * @param \Yuanyou\Risk\Contracts\MessageInterface     $message
     * @param \Yuanyou\Risk\Support\Config                 $config
     *
     * @return array
     *
     * @throws \Yuanyou\Risk\Exceptions\GatewayErrorException
     */
    public function send(PhoneNumberInterface $to, MessageInterface $message, Config $config)
    {
        $params = [
            'smsUser' => $config->get('sms_user'),
            'templateId' => $message->getTemplate($this),
            'msgType' => $to->getIDDCode() ? 2 : 0,
            'phone' => $to->getZeroPrefixedNumber(),
            'vars' => $this->formatTemplateVars($message->getData($this)),
        ];

        if ($config->get('timestamp', false)) {
            $params['timestamp'] = time() * 1000;
        }

        $params['signature'] = $this->sign($params, $config->get('sms_key'));

        $result = $this->post(sprintf(self::ENDPOINT_TEMPLATE, 'send'), $params);

        if (!$result['result']) {
            throw new GatewayErrorException($result['message'], $result['statusCode'], $result);
        }

        return $result;
    }

    /**
     * @param array $vars
     *
     * @return string
     */
    protected function formatTemplateVars(array $vars)
    {
        $formatted = [];

        foreach ($vars as $key => $value) {
            $formatted[sprintf('%%%s%%', trim($key, '%'))] = $value;
        }

        return json_encode($formatted, JSON_FORCE_OBJECT);
    }

    /**
     * @param array  $params
     * @param string $key
     *
     * @return string
     */
    protected function sign($params, $key)
    {
        ksort($params);

        return md5(sprintf('%s&%s&%s', $key, urldecode(http_build_query($params)), $key));
    }
}
