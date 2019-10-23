<?php

/*
 * This file is part of the yuanyou/easy-sms.
 *
 * (c) yuanyou <wuwanhui@yeah.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Yuanyou\EasySms\Gateways;

use Yuanyou\EasySms\Contracts\MessageInterface;
use Yuanyou\EasySms\Contracts\PhoneNumberInterface;
use Yuanyou\EasySms\Exceptions\GatewayErrorException;
use Yuanyou\EasySms\Support\Config;
use Yuanyou\EasySms\Traits\HasHttpRequest;

/**
 * Class JuheGateway.
 *
 * @see https://www.juhe.cn/docs/api/id/54
 */
class JuheGateway extends Gateway
{
    use HasHttpRequest;

    const ENDPOINT_URL = 'http://v.juhe.cn/sms/send';

    const ENDPOINT_FORMAT = 'json';

    /**
     * @param \Yuanyou\EasySms\Contracts\PhoneNumberInterface $to
     * @param \Yuanyou\EasySms\Contracts\MessageInterface     $message
     * @param \Yuanyou\EasySms\Support\Config                 $config
     *
     * @return array
     *
     * @throws \Yuanyou\EasySms\Exceptions\GatewayErrorException ;
     */
    public function send(PhoneNumberInterface $to, MessageInterface $message, Config $config)
    {
        $params = [
            'mobile' => $to->getNumber(),
            'tpl_id' => $message->getTemplate($this),
            'tpl_value' => $this->formatTemplateVars($message->getData($this)),
            'dtype' => self::ENDPOINT_FORMAT,
            'key' => $config->get('app_key'),
        ];

        $result = $this->get(self::ENDPOINT_URL, $params);

        if ($result['error_code']) {
            throw new GatewayErrorException($result['reason'], $result['error_code'], $result);
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
            $formatted[sprintf('#%s#', trim($key, '#'))] = $value;
        }

        return http_build_query($formatted);
    }
}
