<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\MemberBundle\Module;

use Contao\System;
use HeimrichHannot\FormHybrid\FormHelper;
use HeimrichHannot\FormHybrid\FormSession;
use HeimrichHannot\MemberBundle\Form\MemberRegistrationPlusForm;
use Patchwork\Utf8;

class ModuleRegistrationPlus extends \ModuleRegistration
{
    protected $strTemplate = 'mod_registration_plus';

    /**
     * @var MemberRegistrationPlusForm
     */
    protected $form;

    public function generate()
    {
        if (System::getContainer()->get('huh.utils.container')->isBackend()) {
            $objTemplate = new \BackendTemplate('be_wildcard');
            $objTemplate->wildcard = '### '.Utf8::strtoupper($GLOBALS['TL_LANG']['FMD']['registration_plus'][0]).' ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id='.$this->id;

            return $objTemplate->parse();
        }

        $strFormId = FormHelper::getFormId($this->formHybridDataContainer, $this->id);

        // get id from FormSession
        if ($_POST) {
            $intId = FormSession::getSubmissionId($strFormId);
        }

        $this->form = new MemberRegistrationPlusForm($this->objModel, $intId ?: 0);

        $this->editable = $this->form->getEditableFields();

        // Return if there are no editable fields
        if (!is_array($this->editable) || empty($this->editable)) {
            return '';
        }

        return parent::generate();
    }

    protected function compile()
    {
        // render messages before if existing, otherwise error messages will not be displayed after redirect/reload
        if (System::getContainer()->get('huh.member.manager.message')->hasMessages()) {
            $this->Template->message = System::getContainer()->get('huh.member.manager.message')->generate();
        }

        // Activate account
        if ('' !== System::getContainer()->get('huh.request')->getGet('token')) {
            System::getContainer()->get('huh.member.manager.registration')->activateAccount($this->objModel);
        }

        $this->Template->form = $this->form->generate();
    }
}
