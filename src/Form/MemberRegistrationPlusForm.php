<?php

/*
 * Copyright (c) 2019 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\MemberBundle\Form;

use Contao\Config;
use Contao\Controller;
use Contao\DataContainer;
use Contao\Environment;
use Contao\Idna;
use Contao\MemberModel;
use Contao\PageModel;
use Contao\System;

class MemberRegistrationPlusForm extends \HeimrichHannot\FormHybrid\Form
{
    protected $strTemplate = 'formhybrid_registration_plus';

    public function __construct($objModule, $intId = 0)
    {
        $this->strPalette = 'default';
        $this->strMethod = FORMHYBRID_METHOD_POST;

        parent::__construct($objModule, $intId);
    }

    public function modifyDC(&$arrDca = null)
    {
        if (!$this->objModule->disableCaptcha) {
            $this->addEditableField('captcha', $this->dca['fields']['captcha']);
        }

        // HOOK: send insert ID and user data
        if (isset($GLOBALS['TL_HOOKS']['modifyDCRegistrationPlusForm']) && \is_array($GLOBALS['TL_HOOKS']['modifyDCRegistrationPlusForm'])) {
            foreach ($GLOBALS['TL_HOOKS']['modifyDCRegistrationPlusForm'] as $callback) {
                $this->import($callback[0]);
                $this->{$callback[0]}->{$callback[1]}($this->dca, $this->objModule);
            }
        }
    }

    public function getEditableFields()
    {
        if ($this->getFields()) {
            return $this->arrEditable;
        }

        return [];
    }

    protected function setDefaults($arrDca = [])
    {
        parent::setDefaults($arrDca);

        $this->objActiveRecord->login = true;
    }

    protected function modifyVersion($version)
    {
        $version->setUsername($this->objActiveRecord->email);
        $version->setEditUrl('contao/main.php?do=member&act=edit&id='.$this->objActiveRecord->id.'&rt='.REQUEST_TOKEN);

        return $version;
    }

    protected function onSubmitCallback(DataContainer $dc)
    {
        // HOOK: send insert ID and user data
        if (isset($GLOBALS['TL_HOOKS']['preRegistration']) && \is_array($GLOBALS['TL_HOOKS']['preRegistration'])) {
            foreach ($GLOBALS['TL_HOOKS']['preRegistration'] as $callback) {
                $this->import($callback[0]);
                $this->{$callback[0]}->{$callback[1]}($dc->activeRecord->id, $dc->activeRecord, $this->objModule);
            }
        }

        $objMember = System::getContainer()->get('contao.framework')->getAdapter(MemberModel::class)->findByPk($dc->activeRecord->id);

        $objMember->login = $this->objModule->reg_allowLogin;
        $objMember->activation = md5(uniqid(mt_rand(), true));
        $objMember->dateAdded = $dc->activeRecord->tstamp;

        // Set default groups
        if (empty($objMember->groups)) {
            $objMember->groups = $this->objModule->reg_groups;
        }

        // Disable account
        $objMember->disable = 1;

        $objMember->save();

        if ($this->objModule->reg_activate_plus) {
            $this->formHybridSendConfirmationViaEmail = true;
        }

        // HOOK: send insert ID and user data
        if (isset($GLOBALS['TL_HOOKS']['createNewUser']) && \is_array($GLOBALS['TL_HOOKS']['createNewUser'])) {
            foreach ($GLOBALS['TL_HOOKS']['createNewUser'] as $callback) {
                $this->import($callback[0]);
                $this->{$callback[0]}->{$callback[1]}($objMember->id, $objMember->row(), $this->objModule);
            }
        }

        //		$this->setReset(false); // debug - stay on current page
    }

    protected function afterSubmitCallback(\DataContainer $dc)
    {
        /** @var PageModel $target */
        if (null !== ($target = System::getContainer()->get('contao.framework')->getAdapter(PageModel::class)->findPublishedById($this->objModule->jumpTo))) {
            Controller::redirect($target->getFrontendUrl());
        }
    }

    protected function prepareSubmissionData()
    {
        $arrSubmissionData = parent::prepareSubmissionData();

        $arrSubmissionData['domain'] = Idna::decode(Environment::get('host'));
        $arrSubmissionData['activation'] = $this->getActivation();

        if (\in_array('newsletter', System::getContainer()->get('huh.utils.container')->getActiveBundles(), true)) {
            // Replace the wildcard
            if (!empty($this->objModel->newsletter)) {
                $objChannels = \Contao\NewsletterChannelModel::findByIds($this->activeRecord->newsletter);

                if (null !== $objChannels) {
                    $arrSubmissionData['channels'] = implode("\n", $objChannels->fetchEach('title'));
                }
            }
        }

        // Backwards compatibility
        $arrSubmissionData['channel'] = $arrSubmissionData['channels'];

        return $arrSubmissionData;
    }

    protected function compile()
    {
    }

    /**
     * @return string
     */
    protected function getActivation()
    {
        $redirect = Environment::get('base');

        $redirect .= $this->reg_jumpTo ? System::getContainer()->get('huh.utils.url')->getJumpToPageObject($this->reg_jumpTo)->alias : Environment::get('request');

        $redirect .= ((Config::get('disableAlias') || false !== strpos(Environment::get('request'), '?')) ? '&' : '?').'token='.$this->activeRecord->activation;

        return Idna::decode($redirect);
    }
}
