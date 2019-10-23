<?php

/*
 * This file is part of the yuanyou/easy-sms.
 *
 * (c) yuanyou <wuwanhui@yeah.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Yuanyou\Risk\Contracts;

/**
 * Interface PhoneNumberInterface.
 *
 * @author yuanyou <wuwanhui@yeah.net>
 */
interface PhoneNumberInterface extends \JsonSerializable
{
  
    /**
     * 18888888888.
     *
     * @return int
     */
    public function getNumber();
 

    /**
     * @return string
     */
    public function __toString();
}
