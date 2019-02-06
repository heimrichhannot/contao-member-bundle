<?php

/*
 * Copyright (c) 2019 Heimrich & Hannot GmbH
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
use HeimrichHannot\MemberBundle\Model\MemberAddressModel;
use HeimrichHannot\UtilsBundle\Database\DatabaseUtil;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormFactoryInterface;

class CityTypeTest extends ContaoTestCase
{
    public static function tearDownAfterClass(): void
    {
        // The temporary directory would not be removed without this call!
        parent::tearDownAfterClass();
    }

    public function setUp()
    {
        parent::setUp();

        $fs = new Filesystem();
        $fs->mkdir($this->getTempDir().'/assets/images');

        if (!defined('TL_ROOT')) {
            define('TL_ROOT', $this->getTempDir());
        }

        $GLOBALS['TL_LANGUAGE'] = 'de';

        $translator = $this->mockAdapter([]);
        $choiceType = $this->mockAdapter(['getCachedChoices']);
        $choiceType->method('getCachedChoices')->willReturn(['choices']);

        $container = $this->mockContainer();
        $container->setParameter('contao.resources_paths', [__DIR__.'/../../vendor/contao/core-bundle/src/Resources/contao']);
        $container->setParameter('contao.image.target_dir', $this->getTempDir().'/assets/images');
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

    public function testBuildForm()
    {
        $filterConfig = $this->createMock(FilterConfig::class);
        $filterConfig->method('getData')->willReturn(['city' => 'Dresden']);
        $filterConfig->method('getFilter')->willReturn(['dataContainer' => 'tl_member']);
        $cityType = new CityType($filterConfig);

        $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] = '';

        $element = $this->mockClassWithProperties(FilterConfigElementModel::class, ['field' => 'city']);
        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $factory = $this->createMock(FormFactoryInterface::class);
        $builder = new FormBuilder('form', MemberAddressModel::class, $dispatcher, $factory);
        $cityType->buildForm($element, $builder);
        $this->assertTrue($builder->has('city'));
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
