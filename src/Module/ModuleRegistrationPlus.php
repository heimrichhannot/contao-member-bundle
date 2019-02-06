<?php

/*
 * Copyright (c) 2019 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\MemberBundle\Module;

use Contao\Email;
use Contao\FilesModel;
use Contao\Folder;
use Contao\Idna;
use Contao\MemberModel;
use Contao\ModuleRegistration;
use Contao\NewsletterChannelModel;
use Contao\StringUtil;
use Contao\System;
use Contao\Versions;
use HeimrichHannot\FormHybrid\FormHelper;
use HeimrichHannot\FormHybrid\FormSession;
use HeimrichHannot\MemberBundle\Form\MemberRegistrationPlusForm;
use Patchwork\Utf8;

class ModuleRegistrationPlus extends ModuleRegistration
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

        $strFormId = System::getContainer()->get('contao.framework')->getAdapter(FormHelper::class)->getFormId($this->formHybridDataContainer, $this->id);

        // get id from FormSession
        if ($_POST) {
            $intId = System::getContainer()->get('contao.framework')->getAdapter(FormSession::class)->getSubmissionId($strFormId);
        }

        $this->form = new MemberRegistrationPlusForm($this->objModel, $intId ?: 0);

        $this->editable = $this->form->getEditableFields();

        // Return if there are no editable fields
        if (!is_array($this->editable) || empty($this->editable)) {
            return '';
        }

        return parent::generate();
    }

    /**
     * Create a new user and redirect.
     *
     * @param array $data
     */
    public function createNewUser($data)
    {
        $data['tstamp'] = time();
        $data['login'] = $this->reg_allowLogin;
        $data['activation'] = md5(uniqid(mt_rand(), true));
        $data['dateAdded'] = $data['tstamp'];

        // Set default groups
        if (!array_key_exists('groups', $data)) {
            $data['groups'] = $this->reg_groups;
        }

        // Disable account
        $data['disable'] = 1;

        // Send activation e-mail
        if ($this->reg_activate) {
            // Prepare the simple token data
            $arrTokenData = $data;
            $arrTokenData['domain'] = Idna::decode(\Environment::get('host'));
            $arrTokenData['link'] = Idna::decode(\Environment::get('base')).\Environment::get('request').((\Config::get('disableAlias')
                                                                                                                   || false !== strpos(\Environment::get('request'), '?')) ? '&' : '?').'token='.$data['activation'];
            $arrTokenData['channels'] = '';

            if (in_array('newsletter', System::getContainer()->get('huh.utils.container')->getActiveBundles(), true)) {
                // Make sure newsletter is an array
                if (!is_array($data['newsletter'])) {
                    if ('' !== $data['newsletter']) {
                        $data['newsletter'] = [$data['newsletter']];
                    } else {
                        $data['newsletter'] = [];
                    }
                }

                // Replace the wildcard
                if (!empty($data['newsletter'])) {
                    $objChannels = System::getContainer()->get('contao.framework')->getAdapter(NewsletterChannelModel::class)->findByIds($data['newsletter']);

                    if (null !== $objChannels) {
                        $arrTokenData['channels'] = implode("\n", $objChannels->fetchEach('title'));
                    }
                }
            }

            // Backwards compatibility
            $arrTokenData['channel'] = $arrTokenData['channels'];

            $objEmail = new Email();

            $objEmail->from = $GLOBALS['TL_ADMIN_EMAIL'];
            $objEmail->fromName = $GLOBALS['TL_ADMIN_NAME'];
            $objEmail->subject = sprintf($GLOBALS['TL_LANG']['MSC']['emailSubject'], Idna::decode(\Environment::get('host')));
            $objEmail->text = StringUtil::parseSimpleTokens($this->reg_text, $arrTokenData);
            $objEmail->sendTo($data['email']);
        }

        // Make sure newsletter is an array
        if (isset($data['newsletter']) && !is_array($data['newsletter'])) {
            $data['newsletter'] = [$data['newsletter']];
        }

        // Create the user
        $objNewUser = new MemberModel();
        $objNewUser->setRow($data);
        $objNewUser->save();

        // Assign home directory
        if ($this->reg_assignDir) {
            $objHomeDir = System::getContainer()->get('contao.framework')->getAdapter(FilesModel::class)->findByUuid($this->reg_homeDir);

            if (null !== $objHomeDir) {
                $this->import('Files');
                $strUserDir = StringUtil::standardize($data['username']) ?: 'user_'.$objNewUser->id;

                // Add the user ID if the directory exists
                while (is_dir(TL_ROOT.'/'.$objHomeDir->path.'/'.$strUserDir)) {
                    $strUserDir .= '_'.$objNewUser->id;
                }

                // Create the user folder
                new Folder($objHomeDir->path.'/'.$strUserDir);

                $objUserDir = System::getContainer()->get('contao.framework')->getAdapter(FilesModel::class)->findByPath($objHomeDir->path.'/'.$strUserDir);

                // Save the folder ID
                $objNewUser->assignDir = 1;
                $objNewUser->homeDir = $objUserDir->uuid;
                $objNewUser->save();
            }
        }

        // HOOK: send insert ID and user data
        if (isset($GLOBALS['TL_HOOKS']['createNewUser']) && is_array($GLOBALS['TL_HOOKS']['createNewUser'])) {
            foreach ($GLOBALS['TL_HOOKS']['createNewUser'] as $callback) {
                $this->import($callback[0]);
                $this->{$callback[0]}->{$callback[1]}($objNewUser->id, $data, $this);
            }
        }

        // Create the initial version (see #7816)
        $objVersions = new Versions('tl_member', $objNewUser->id);
        $objVersions->setUsername($objNewUser->username);
        $objVersions->setUserId(0);
        $objVersions->setEditUrl('contao/main.php?do=member&act=edit&id=%s&rt=1');
        $objVersions->initialize();

        // Inform admin if no activation link is sent
        if (!$this->reg_activate) {
            $this->sendAdminNotification($objNewUser->id, $data);
        }

        // Check whether there is a jumpTo page
        if (null !== ($objJumpTo = $this->objModel->getRelated('jumpTo'))) {
            $this->jumpToOrReload($objJumpTo->row());
        }

        $this->reload();
    }

    protected function compile()
    {
        // render messages before if existing, otherwise error messages will not be displayed after redirect/reload
        if (System::getContainer()->get('huh.member.manager.message')->hasMessages()) {
            $this->Template->message = System::getContainer()->get('huh.member.manager.message')->generate();
        }

        // Activate account
        if (System::getContainer()->get('huh.request')->hasGet('token')) {
            System::getContainer()->get('huh.member.manager.registration')->activateAccount($this->objModel);
        }

        $this->Template->form = $this->form->generate();
    }
}
