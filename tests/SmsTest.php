<?php


namespace Yuanyou\Risk\Tests;

use PHPUnit\Framework\TestCase;
use Yuanyou\Risk\Risk;

/**
 * Class EasySms.
 */
class SmsTest extends TestCase
{

    public function testEat()
    {
        $config = [
            'url' => 'http://risk.4255.cn/',
            'token' => 'b08d2c0f36e6d39cfa2b76f9b9380615c10eb154d606ecafc7d0eff19a44cbc2342341',
            'secret_key' => 'fb2be0d575174c0ab3d5ea2923423423412bcda31',
        ];


        $risk = new Risk();
        $risk->setConfig($config);
        //短信
//        $result = $risk->MessageSend('13983087666', 33, ["code" => 1111]);
//银行卡四要素
//        $result = $risk->CertificationBankFour('吴红', '13983087666',"512322198112202344917","6221342342340013257480");
        //手机三要素（未验证）
//        $result = $risk->CertificationMobileThree('吴红', '13983087666', "512322198112204917");
//        return var_dump($result);
    }

}
