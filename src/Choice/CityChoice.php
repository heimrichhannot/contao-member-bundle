<?php

/*
 * Copyright (c) 2020 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\MemberBundle\Choice;

use Contao\StringUtil;
use Contao\System;
use HeimrichHannot\UtilsBundle\Choice\AbstractChoice;

class CityChoice extends AbstractChoice
{
    protected function collect()
    {
        $addresses = [];

        $members = System::getContainer()->get('huh.member.entity.member')->findAll();

        if (null === $members) {
            return $addresses;
        }

        foreach ($members as $member) {
            if ('' !== $member->city) {
                $addresses[$member->city] = $member->city;
            }
            if (null === $member->additionalAddresses) {
                continue;
            }
            $additionalAddresses = StringUtil::deserialize($member->additionalAddresses, true);

            foreach ($additionalAddresses as $additionalAddress) {
                $address = System::getContainer()->get('huh.member.entity.address')->findById($additionalAddress);
                if (null === $address || '' === $address->city) {
                    continue;
                }
                $addresses[$address->city] = $address->city;
            }
        }

        return $addresses;
    }
}
