<?php

/*
 * Copyright (c) 2019 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\MemberBundle\Test\Entity;

use Contao\TestCase\ContaoTestCase;
use HeimrichHannot\MemberBundle\Entity\MemberAddress;
use HeimrichHannot\MemberBundle\Model\MemberAddressModel;

class MemberAddressTest extends ContaoTestCase
{
    public function testFindBy()
    {
        $memberAddressAdapter = $this->mockAdapter(['findBy']);
        $memberAddressAdapter->method('findBy')->willReturn(true);

        $memberAddress = new MemberAddress($this->mockContaoFramework([MemberAddressModel::class => $memberAddressAdapter]));
        $result = $memberAddress->findByPid(1);
        $this->assertTrue($result);

        $result = $memberAddress->findById(1);
        $this->assertTrue($result);

        $result = $memberAddress->findByPidAndCity(1, 'Dresden');
        $this->assertTrue($result);
    }
}
