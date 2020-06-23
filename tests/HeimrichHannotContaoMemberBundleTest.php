<?php

/*
 * Copyright (c) 2020 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\MemberBundle\Test;

use Contao\TestCase\ContaoTestCase;
use HeimrichHannot\MemberBundle\DependencyInjection\HeimrichHannotContaoMemberBundleExtension;
use HeimrichHannot\MemberBundle\HeimrichHannotContaoMemberBundle;

class HeimrichHannotContaoMemberBundleTest extends ContaoTestCase
{
    public function testGetContainerExtension()
    {
        $bundle = new HeimrichHannotContaoMemberBundle();

        $this->assertInstanceOf(HeimrichHannotContaoMemberBundleExtension::class, $bundle->getContainerExtension());
    }
}
