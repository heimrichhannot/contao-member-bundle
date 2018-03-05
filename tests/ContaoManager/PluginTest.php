<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\MemberBundle\Test\ContaoManager;

use Contao\TestCase\ContaoTestCase;
use HeimrichHannot\MemberBundle\ContaoManager\Plugin;

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
