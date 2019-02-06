<?php

/*
 * Copyright (c) 2019 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\MemberBundle\Test\ContaoManager;

use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\DelegatingParser;
use Contao\ManagerPlugin\PluginLoader;
use Contao\TestCase\ContaoTestCase;
use HeimrichHannot\MemberBundle\ContaoManager\Plugin;
use HeimrichHannot\MemberBundle\HeimrichHannotContaoMemberBundle;
use PHPUnit\Framework\MockObject\Matcher\Invocation;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class PluginTest extends ContaoTestCase
{
    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();
        $this->container = new \Contao\ManagerPlugin\Config\ContainerBuilder($this->mockPluginLoader($this->never()), []);
    }

    public function testInstantiation()
    {
        $this->assertInstanceOf(Plugin::class, new Plugin());
    }

    public function testGetBundles()
    {
        $plugin = new Plugin();
        $bundles = $plugin->getBundles(new DelegatingParser());
        $this->assertCount(1, $bundles);
        $this->assertInstanceOf(BundleConfig::class, $bundles[0]);
        $this->assertSame(HeimrichHannotContaoMemberBundle::class, $bundles[0]->getName());
    }

    public function testGetExtensionConfig()
    {
        $plugin = new Plugin();
        $extensionConfigs = $plugin->getExtensionConfig('huh_filter', [[]], $this->container);
        $this->assertNotEmpty($extensionConfigs);
        $this->assertArrayHasKey('huh', $extensionConfigs);
        $this->assertArrayHasKey('filter', $extensionConfigs['huh']);
    }

    /**
     * Mocks the plugin loader.
     *
     * @param Invocation $expects
     * @param array      $plugins
     *
     * @return PluginLoader|\PHPUnit_Framework_MockObject_MockObject
     */
    private function mockPluginLoader(Invocation $expects, array $plugins = [])
    {
        $pluginLoader = $this->createMock(PluginLoader::class);
        $pluginLoader->expects($expects)->method('getInstancesOf')->with(PluginLoader::EXTENSION_PLUGINS)->willReturn($plugins);

        return $pluginLoader;
    }
}
