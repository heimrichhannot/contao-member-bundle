<?php

/*
 * Copyright (c) 2019 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\MemberBundle;

use HeimrichHannot\MemberBundle\DependencyInjection\HeimrichHannotContaoMemberBundleExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class HeimrichHannotContaoMemberBundle extends Bundle
{
    /**
     * @return HeimrichHannotContaoMemberBundleExtension
     */
    public function getContainerExtension()
    {
        return new HeimrichHannotContaoMemberBundleExtension();
    }
}
