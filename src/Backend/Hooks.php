<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\MemberBundle\Backend;

use Contao\Module;
use Contao\System;
use HeimrichHannot\FilterBundle\Config\FilterConfig;
use HeimrichHannot\ListBundle\Model\ListConfigModel;
use HeimrichHannot\UtilsBundle\Driver\DC_Table_Utils;

class Hooks
{
    public function switchAddress($result, $item, Module $moduleList, FilterConfig $filterConfig, ListConfigModel $listConfig)
    {
        $filter = $filterConfig->getData();

        if (!isset($filter['city'])) {
            return $result;
        }

        if ($result['formatted']['city'] === $filter['city']) {
            return $result;
        }

        $memberAddress = System::getContainer()->get('huh.member.entity.address')->findByPidAndCity($result['raw']['id'], $filter['city']);

        if (null === $memberAddress) {
            return $result;
        }
        $dataContainer = $filterConfig->getFilter()['dataContainer'];
        $formUtil = System::getContainer()->get('huh.utils.form');
        $dc = System::getContainer()->get('contao.framework')->getAdapter(DC_Table_Utils::class)->createFromModelData($item, $dataContainer);

        $result['formatted']['city'] = $formUtil->prepareSpecialValueForOutput('city', $memberAddress->city, $dc);
        $result['formatted']['city'] = $formUtil->escapeAllHtmlEntities($dataContainer, 'city', $result['formatted']['city']);
        $result['formatted']['country'] = $formUtil->prepareSpecialValueForOutput('country', $memberAddress->country, $dc);
        $result['formatted']['country'] = $formUtil->escapeAllHtmlEntities($dataContainer, 'country', $result['formatted']['country']);
        $result['formatted']['state'] = $formUtil->prepareSpecialValueForOutput('state', $memberAddress->state, $dc);
        $result['formatted']['state'] = $formUtil->escapeAllHtmlEntities($dataContainer, 'state', $result['formatted']['state']);
        $result['formatted']['postal'] = $formUtil->prepareSpecialValueForOutput('postal', $memberAddress->postal, $dc);
        $result['formatted']['postal'] = $formUtil->escapeAllHtmlEntities($dataContainer, 'postal', $result['formatted']['postal']);
        $result['formatted']['street'] = $formUtil->prepareSpecialValueForOutput('street', $memberAddress->street, $dc);
        $result['formatted']['street'] = $formUtil->escapeAllHtmlEntities($dataContainer, 'street', $result['formatted']['street']);
        $result['formatted']['company'] = $formUtil->prepareSpecialValueForOutput('company', $memberAddress->company, $dc);
        $result['formatted']['company'] = $formUtil->escapeAllHtmlEntities($dataContainer, 'company', $result['formatted']['company']);
        $result['formatted']['phone'] = $formUtil->prepareSpecialValueForOutput('phone', $memberAddress->phone, $dc);
        $result['formatted']['phone'] = $formUtil->escapeAllHtmlEntities($dataContainer, 'phone', $result['formatted']['phone']);
        $result['formatted']['fax'] = $formUtil->prepareSpecialValueForOutput('fax', $memberAddress->fax, $dc);
        $result['formatted']['fax'] = $formUtil->escapeAllHtmlEntities($dataContainer, 'fax', $result['formatted']['fax']);
        $result['formatted']['addressText'] = $formUtil->prepareSpecialValueForOutput('addressText', $memberAddress->addressText, $dc);
        $result['formatted']['addressText'] = $formUtil->escapeAllHtmlEntities($dataContainer, 'addressText', $result['formatted']['addressText']);
        $result['formatted']['street2'] = $formUtil->prepareSpecialValueForOutput('street2', $memberAddress->street2, $dc);
        $result['formatted']['street2'] = $formUtil->escapeAllHtmlEntities($dataContainer, 'street2', $result['formatted']['street2']);

        return $result;
    }
}
