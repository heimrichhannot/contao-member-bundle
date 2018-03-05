<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\MemberBundle\Test\DependencyInjection;

use Contao\TestCase\ContaoTestCase;
use HeimrichHannot\MemberBundle\DependencyInjection\HeimrichHannotContaoMemberBundleExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class HeimrichHannotContaoMemberBundleExtensionTest extends ContaoTestCase
{
    /**
     * @var ContainerBuilder
     */
    private $container;

    public function setUp()
    {
        parent::setUp();

        $this->container = new ContainerBuilder(new ParameterBag(['kernel.debug' => false]));
        $extension = new HeimrichHannotContaoMemberBundleExtension();
        $extension->load([], $this->container);
    }

    /**
     * Tests the object instantiation.
     */
    public function testCanBeInstantiated()
    {
        $extension = new HeimrichHannotContaoMemberBundleExtension();
        $this->assertInstanceOf(HeimrichHannotContaoMemberBundleExtension::class, $extension);
    }
}
