<?php

/*
 * Copyright (c) 2019 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\MemberBundle\Test\Backend;

use Contao\System;
use Contao\TestCase\ContaoTestCase;
use HeimrichHannot\FilterBundle\Config\FilterConfig;
use HeimrichHannot\ListBundle\Model\ListConfigModel;
use HeimrichHannot\ListBundle\Module\ModuleList;
use HeimrichHannot\MemberBundle\Backend\Hooks;
use HeimrichHannot\MemberBundle\Model\MemberAddressModel;
use HeimrichHannot\UtilsBundle\Driver\DC_Table_Utils;

class HooksTest extends ContaoTestCase
{
    public function setUp()
    {
        parent::setUp();

        $container = $this->mockContainer();

        $properties = [
            'city' => 'Dresden',
            'country' => 'de',
            'state' => 'Sachsen',
            'postal' => '01309',
            'street' => 'Bayrischer Weg',
            'phone' => '',
            'fax' => '',
            'addressText' => '',
            'street2' => '',
        ];

        $memberAddress = $this->mockClassWithProperties(MemberAddressModel::class, $properties);
        $entityAddressAdapter = $this->mockAdapter(['findByPidAndCity']);
        $entityAddressAdapter->method('findByPidAndCity')->willReturn($memberAddress);
        $container->set('huh.member.entity.address', $entityAddressAdapter);

        $formUtil = $this->mockAdapter(['prepareSpecialValueForOutput', 'escapeAllHtmlEntities']);
        $formUtil->method('escapeAllHtmlEntities')->willReturnCallback(function ($key, $stuff, $value) { return $value; });
        $formUtil->method('prepareSpecialValueForOutput')->willReturnCallback(function ($key, $value, $dc) { return $value; });
        $container->set('huh.utils.form', $formUtil);

        $dcAdapter = $this->mockAdapter(['createFromModelData']);
        $dcAdapter->method('createFromModelData')->willReturn([]);

        $container->set('contao.framework', $this->mockContaoFramework([DC_Table_Utils::class => $dcAdapter]));

        System::setContainer($container);
    }

    public function testSwitchAddress()
    {
        $hooks = new Hooks();

        $moduleList = $this->createMock(ModuleList::class);
        $filterConfig = $this->createMock(FilterConfig::class);
        $filterConfig->method('getData')->willReturn([]);
        $listConfig = $this->createMock(ListConfigModel::class);

        $result = $hooks->switchAddress(['fail'], [], $moduleList, $filterConfig, $listConfig);
        $this->assertSame(['fail'], $result);

        $filterConfig = $this->createMock(FilterConfig::class);
        $filterConfig->method('getData')->willReturn(['city' => 'Dresden']);

        $result = $hooks->switchAddress(['formatted' => ['city' => 'Dresden']], [], $moduleList, $filterConfig, $listConfig);
        $this->assertSame(['formatted' => ['city' => 'Dresden']], $result);

        $filterConfig = $this->createMock(FilterConfig::class);
        $filterConfig->method('getData')->willReturn(['city' => 'Dresden']);
        $filterConfig->method('getFilter')->willReturn(['dataContainer' => []]);

        $result = $hooks->switchAddress(['formatted' => ['city' => 'Berlin'], 'raw' => ['id' => '2']], [], $moduleList, $filterConfig, $listConfig);
        $this->assertSame('Dresden', $result['formatted']['city']);
        $this->assertSame('de', $result['formatted']['country']);
        $this->assertSame('Sachsen', $result['formatted']['state']);

        $container = System::getContainer();

        $entityAddressAdapter = $this->mockAdapter(['findByPidAndCity']);
        $entityAddressAdapter->method('findByPidAndCity')->willReturn(null);
        $container->set('huh.member.entity.address', $entityAddressAdapter);

        System::setContainer($container);

        $result = $hooks->switchAddress(['formatted' => ['city' => 'Berlin'], 'raw' => ['id' => '2']], [], $moduleList, $filterConfig, $listConfig);
        $this->assertSame('Berlin', $result['formatted']['city']);
    }
}
