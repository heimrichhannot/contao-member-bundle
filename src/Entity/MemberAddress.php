<?php

/*
 * Copyright (c) 2019 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\MemberBundle\Entity;

use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use HeimrichHannot\MemberBundle\Model\MemberAddressModel;

class MemberAddress
{
    /**
     * @var ContaoFrameworkInterface
     */
    protected $framework;

    public function __construct(ContaoFrameworkInterface $framework)
    {
        $this->framework = $framework;
    }

    public function findByPid($pid, array $options = [])
    {
        /** @var MemberAddressModel $adapter */
        $adapter = $this->framework->getAdapter(MemberAddressModel::class);

        return $adapter->findBy('pid', $pid, $options);
    }

    public function findById($id, array $options = [])
    {
        /** @var MemberAddressModel $adapter */
        $adapter = $this->framework->getAdapter(MemberAddressModel::class);

        return $adapter->findBy('id', $id, $options);
    }

    public function findByPidAndCity($pid, $city, array $options = [])
    {
        /** @var MemberAddressModel $adapter */
        $adapter = $this->framework->getAdapter(MemberAddressModel::class);

        return $adapter->findBy(['pid=?', 'city=?'], [$pid, $city], $options);
    }
}
