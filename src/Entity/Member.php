<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\MemberBundle\Entity;

use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Contao\MemberModel;

class Member
{
    /**
     * @var ContaoFrameworkInterface
     */
    protected $framework;

    public function __construct(ContaoFrameworkInterface $framework)
    {
        $this->framework = $framework;
    }

    public function findAll(array $options = [])
    {
        /** @var MemberModel $memberAdapter */
        $memberAdapter = $this->framework->getAdapter(MemberModel::class);

        return $memberAdapter->findAll($options);
    }

    public function findBy($column, $value, array $options = [])
    {
        /** @var MemberModel $memberAdapter */
        $memberAdapter = $this->framework->getAdapter(MemberModel::class);

        return $memberAdapter->findBy($column, $value, $options);
    }
}
