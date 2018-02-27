<?php
/**
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\MemberBundle\Test\ContaoManager;


use HeimrichHannot\MemberBundle\ContaoManager\Plugin;
use Terminal42\ChangeLanguage\Tests\ContaoTestCase;

class PluginTest extends ContaoTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();
    }

    public function testInstantiation()
    {
        $this->assertInstanceOf(Plugin::class, new Plugin());
    }
}