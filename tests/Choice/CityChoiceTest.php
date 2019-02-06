<?php

/*
 * Copyright (c) 2019 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\MemberBundle\Test\Choice;

use Contao\MemberModel;
use Contao\System;
use Contao\TestCase\ContaoTestCase;
use HeimrichHannot\MemberBundle\Choice\CityChoice;
use HeimrichHannot\MemberBundle\Model\MemberAddressModel;

class CityChoiceTest extends ContaoTestCase
{
    public function setUp()
    {
        parent::setUp();

        $container = $this->mockContainer();

        $member1 = $this->mockClassWithProperties(MemberModel::class, ['city' => 'Berlin', 'additionalAddresses' => null]);
        $member2 = $this->mockClassWithProperties(MemberModel::class, ['city' => 'Dresden', 'additionalAddresses' => 'a:2:{i:0;s:2:"11";i:1;s:2:"12";}']);
        $memberAdapter = $this->mockAdapter(['findAll']);
        $memberAdapter->method('findAll')->willReturn([$member1, $member2]);
        $container->set('huh.member.entity.member', $memberAdapter);

        $addressModel = $this->mockClassWithProperties(MemberAddressModel::class, ['city' => 'Hamburg']);
        $addressAdapter = $this->mockAdapter(['findById']);
        $addressAdapter->method('findById')->willReturn($addressModel, null);
        $container->set('huh.member.entity.address', $addressAdapter);

        $kernel = $this->mockAdapter(['getCacheDir']);
        $kernel->method('getCacheDir')->willReturn('');
        $container->set('kernel', $kernel);

        System::setContainer($container);
    }

    public function testCollect()
    {
        $cityChoice = new CityChoice($this->mockContaoFramework());
        $result = $cityChoice->getChoices();
        $this->assertSame(['Berlin' => 'Berlin', 'Dresden' => 'Dresden', 'Hamburg' => 'Hamburg'], $result);

        $container = System::getContainer();
        $memberAdapter = $this->mockAdapter(['findAll']);
        $memberAdapter->method('findAll')->willReturn(null);
        $container->set('huh.member.entity.member', $memberAdapter);
        System::setContainer($container);

        $cityChoice = new CityChoice($this->mockContaoFramework());
        $result = $cityChoice->getChoices();
        $this->assertSame([], $result);
    }
}
