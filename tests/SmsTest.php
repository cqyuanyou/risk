<?php

/*
 * This file is part of the yuanyou/easy-sms.
 *
 * (c) yuanyou <wuwanhui@yeah.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Yuanyou\Risk\Tests;

use PHPUnit\Framework\TestCase;
use Yuanyou\Risk\Message;
use Yuanyou\Risk\SmsService;
use Yuanyou\Risk\Strategies\OrderStrategy;

/**
 * Class EasySms.
 */
class SmsTest extends TestCase
{

    public function testEat()
    {
        $config = [
            // HTTP 请求的超时时间（秒）
            'timeout' => 5.0,
            // 默认发送配置
            'default' => [
                // 网关调用策略，默认：顺序调用
                'strategy' => OrderStrategy::class,

                // 默认可用的发送网关
                'gateways' => [
                    'sms',
                ],
            ],
            // 可用的网关配置
            'gateways' => [
                'errorlog' => [
                    'file' => '/tmp/yuanyou-sms.log',
                ],
                'sms' => [
                    'token' => 'be0c5e4151465c9dcfeb0410401bc6246aac6b619dab0ceb14a1111234ce6e3f',
                    'secret_key' => 'bc619a86ae754aa5bdbf33c19396fc94',
                    'sign_id' => 13,
                ],

            ],
        ];
        $sms = new SmsService($config);

//        $result = $sms->send('13983087661', [
//            'content' => '测试内容',
//            'template' => 33,
//            'data' => ["code" => 1111]
//        ]);

        $this->assertEquals('', $sms->getMessenger());
    }

}
