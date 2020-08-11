<?php

/*
 * Copyright (c) 2020 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\MemberBundle\Backend;

use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Contao\DataContainer;
use Contao\FrontendUser;
use Contao\System;

class Member
{
    /**
     * @var ContaoFrameworkInterface
     */
    private $framework;

    public function __construct(ContaoFrameworkInterface $framework)
    {
        $this->framework = $framework;
    }

    /**
     * Add an URI scheme if not existing to an url.
     */
    public function addURIScheme(string $varValue, DataContainer $dc): string
    {
        return System::getContainer()->get('huh.utils.url')->addURIScheme($varValue);
    }

    /**
     * @param mixed                           $value  Value to be saved
     * @param DataContainer|FrontendUser|null $dc     DataContainer object in backend, front end user instance in frontendmodule Personal data, null in registration frontend module
     * @param null                            $module
     *
     * @throws \Exception
     */
    public function generateAlias($varValue, DataContainer $dc = null): string
    {
        if (!$dc) {
            return '';
        }
        if (null === ($member = System::getContainer()->get('huh.utils.model')->findModelInstanceByPk('tl_member', $dc->id))) {
            return '';
        }

        $parts = array_filter([$dc->activeRecord->academicTitle, $dc->activeRecord->firstname, $dc->activeRecord->nobilityTitle, $dc->activeRecord->lastname]);

        if (empty($parts)) {
            $parts = array_filter([$member->activeRecord->academicTitle, $member->activeRecord->firstname, $member->activeRecord->nobilityTitle, $member->activeRecord->lastname]);
        }

        return System::getContainer()->get('huh.utils.dca')->generateAlias($varValue, $dc->id, 'tl_member', trim(implode(' ', $parts)));
    }

    /**
     * Get a list of job title choices.
     *
     * @return array List of job titles
     */
    public function getJobTitleChoices(DataContainer $dc): array
    {
        $choices = [];

        if (null === ($members = System::getContainer()->get('huh.utils.model')->findModelInstancesBy('tl_member', ['tl_member.jobTitles IS NOT NULL'], null))) {
            return $choices;
        }

        $titles = $members->fetchEach('jobTitles');

        foreach ($titles as $list) {
            $choices = array_merge($choices, deserialize($list, true));
        }

        sort($choices);

        return $choices;
    }
}
