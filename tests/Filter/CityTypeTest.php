<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\MemberBundle\Test\Filter;

use Contao\System;
use Contao\TestCase\ContaoTestCase;
use Doctrine\DBAL\Connection;
use HeimrichHannot\FilterBundle\Config\FilterConfig;
use HeimrichHannot\FilterBundle\Model\FilterConfigElementModel;
use HeimrichHannot\FilterBundle\QueryBuilder\FilterQueryBuilder;
use HeimrichHannot\MemberBundle\Filter\CityType;
use HeimrichHannot\UtilsBundle\Database\DatabaseUtil;

class CityTypeTest extends ContaoTestCase
{
    public function setUp()
    {
        parent::setUp();

        $translator = $this->mockAdapter([]);
        $choiceType = $this->mockAdapter(['getCachedChoices']);
        $choiceType->method('getCachedChoices')->willReturn(['choices']);

        $container = $this->mockContainer();
        $container->set('translator', $translator);
        $container->set('huh.member.choice.city', $choiceType);

        System::setContainer($container);
    }

    public function testGetDefaultOperator()
    {
        $filterConfig = $this->createMock(FilterConfig::class);
        $cityType = new CityType($filterConfig);
        $result = $cityType->getDefaultOperator($this->getFilterConfigElementModel());
        $this->assertSame(DatabaseUtil::OPERATOR_EQUAL, $result);
    }

    public function testGetChoices()
    {
        $filterConfig = $this->createMock(FilterConfig::class);
        $cityType = new CityType($filterConfig);
        $result = $cityType->getChoices($this->getFilterConfigElementModel());
        $this->assertSame(['choices'], $result);
    }

    public function testBuildQuery()
    {
        $filterConfig = $this->createMock(FilterConfig::class);
        $filterConfig->method('getData')->willReturn(['city' => 'Dresden']);
        $filterConfig->method('getFilter')->willReturn(['dataContainer' => 'tl_member']);
        $cityType = new CityType($filterConfig);

        $element = $this->mockClassWithProperties(FilterConfigElementModel::class, ['field' => 'city']);
        $filterQueryBuilder = new FilterQueryBuilder($this->mockContaoFramework(), $this->getConnection());
        $cityType->buildQuery($filterQueryBuilder, $element);
        $this->assertSame('SELECT  WHERE (tl_member.city= :cityValue) OR (tl_member_address_city.city= :tl_member_address_city) GROUP BY tl_member.id', $filterQueryBuilder->getSQL());
    }

    /**
     * @return FilterConfigElementModel | \PHPUnit\Framework\MockObject\MockObject
     */
    public function getFilterConfigElementModel()
    {
        return $this->createMock(FilterConfigElementModel::class);
    }

    /**
     * @return Connection | \PHPUnit\Framework\MockObject\MockObject
     */
    public function getConnection()
    {
        return $this->createMock(Connection::class);
    }
}
