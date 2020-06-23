<?php

/*
 * Copyright (c) 2020 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\MemberBundle\Test\Entity;

use Contao\MemberModel;
use Contao\TestCase\ContaoTestCase;
use HeimrichHannot\MemberBundle\Entity\Member;

class MemberTest extends ContaoTestCase
{
    public function testFindBy()
    {
        $memberAddressAdapter = $this->mockAdapter(['findBy', 'findAll']);
        $memberAddressAdapter->method('findBy')->willReturn(true);
        $memberAddressAdapter->method('findAll')->willReturn(true);

        $memberAddress = new Member($this->mockContaoFramework([MemberModel::class => $memberAddressAdapter]));
        $result = $memberAddress->findBy('column', 'value');
        $this->assertTrue($result);

        $result = $memberAddress->findAll();
        $this->assertTrue($result);
    }
}
