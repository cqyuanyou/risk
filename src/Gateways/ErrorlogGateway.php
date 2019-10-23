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
use Yuanyou\EasySms\Support\Config;

/**
 * Class ErrorlogGateway.
 */
class ErrorlogGateway extends Gateway
{
    /**
     * @param \Yuanyou\EasySms\Contracts\PhoneNumberInterface $to
     * @param \Yuanyou\EasySms\Contracts\MessageInterface     $message
     * @param \Yuanyou\EasySms\Support\Config                 $config
     *
     * @return array
     */
    public function send(PhoneNumberInterface $to, MessageInterface $message, Config $config)
    {
        if (is_array($to)) {
            $to = implode(',', $to);
        }

        $message = sprintf(
            "[%s] to: %s | message: \"%s\"  | template: \"%s\" | data: %s\n",
            date('Y-m-d H:i:s'),
            $to,
            $message->getContent($this),
            $message->getTemplate($this),
            json_encode($message->getData($this))
        );

        $file = $this->config->get('file', ini_get('error_log'));
        $status = error_log($message, 3, $file);

        return compact('status', 'file');
    }
}
