<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\MemberBundle\Manager;

use Contao\Controller;
use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Contao\MemberModel;
use Contao\Model;
use Contao\System;

class RegistrationManager
{
    /**
     * @var ContaoFrameworkInterface
     */
    protected $framework;

    public function __construct(ContaoFrameworkInterface $framework)
    {
        $this->framework = $framework;
    }

    /**
     * Activate an account.
     */
    public function activateAccount(Model $model)
    {
        $hasError = false;
        $strReloadUrl = preg_replace('/(&|\?)token=[^&]*/', '', \Environment::get('request')); // remove token from url

        $objMember = $this->framework->getAdapter(MemberModel::class)->findByActivation(MEMBER_ACTIVATION_ACTIVATED_FIELD_PREFIX.\Input::get('token'));

        // member with this token already activated
        if (null !== $objMember) {
            $hasError = true;
            System::getContainer()->get('huh.member.manager.message')->addDanger($GLOBALS['TL_LANG']['MSC']['alreadyActivated']);
        }

        // check for invalid token
        if (!$hasError) {
            $objMember = \MemberModel::findByActivation(\Input::get('token'));

            if (null === $objMember) {
                $hasError = true;
                System::getContainer()->get('huh.member.manager.message')->addDanger($GLOBALS['TL_LANG']['MSC']['invalidActivationToken']);
            }
        }

        // if has errors, remove token from url and redirect to current page without token parameter
        if ($hasError) {
            $this->framework->getAdapter(Controller::class)->redirect($strReloadUrl);
        }

        // Update the account
        $objMember->disable = '';
        $objMember->activation = MEMBER_ACTIVATION_ACTIVATED_FIELD_PREFIX.$objMember->activation;
        $objMember->save();

        $this->accountActivatedMessage = $GLOBALS['TL_LANG']['MSC']['accountActivated'];

        // HOOK: post activation callback
        if (isset($GLOBALS['TL_HOOKS']['activateAccount']) && is_array($GLOBALS['TL_HOOKS']['activateAccount'])) {
            foreach ($GLOBALS['TL_HOOKS']['activateAccount'] as $callback) {
                System::importStatic($callback[0]);
                $this->{$callback[0]}->{$callback[1]}($objMember, $this);
            }
        }

        // Log activity
        System::getContainer()->get('monolog.logger.contao')->log('User account ID '.$objMember->id.' ('.$objMember->email.') has been activated', __METHOD__, TL_ACCESS);
        // Redirect to the jumpTo page
        if (null !== ($objTarget = $model->getRelated('reg_jumpTo'))) {
//            $this->redirect($this->generateFrontendUrl($objTarget->row()));
            $this->framework->getAdapter(Controller::class)->redirect($objTarget->getFrontendUrl());
        } // redirect to current page without token parameter
        else {
            System::getContainer()->get('huh.member.manager.message')->addSuccess($this->accountActivatedMessage);
            $this->framework->getAdapter(Controller::class)->redirect($strReloadUrl);
        }
    }

    /**
     * Create a new user and redirect.
     *
     * @param array $data
     */
    public function createNewUser(array $data)
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
            $arrTokenData['domain'] = \Idna::decode(\Environment::get('host'));
            $arrTokenData['link'] = \Idna::decode(\Environment::get('base')).\Environment::get('request').((\Config::get('disableAlias')
                                                                                                                    || false !== strpos(\Environment::get('request'), '?')) ? '&' : '?').'token='.$data['activation'];
            $arrTokenData['channels'] = '';

            if (in_array('newsletter', \ModuleLoader::getActive(), true)) {
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
                    $objChannels = \NewsletterChannelModel::findByIds($data['newsletter']);

                    if (null !== $objChannels) {
                        $arrTokenData['channels'] = implode("\n", $objChannels->fetchEach('title'));
                    }
                }
            }

            // Backwards compatibility
            $arrTokenData['channel'] = $arrTokenData['channels'];

            $objEmail = new \Email();

            $objEmail->from = $GLOBALS['TL_ADMIN_EMAIL'];
            $objEmail->fromName = $GLOBALS['TL_ADMIN_NAME'];
            $objEmail->subject = sprintf($GLOBALS['TL_LANG']['MSC']['emailSubject'], \Idna::decode(\Environment::get('host')));
            $objEmail->text = \StringUtil::parseSimpleTokens($this->reg_text, $arrTokenData);
            $objEmail->sendTo($data['email']);
        }

        // Make sure newsletter is an array
        if (isset($data['newsletter']) && !is_array($data['newsletter'])) {
            $data['newsletter'] = [$data['newsletter']];
        }

        // Create the user
        $objNewUser = new \MemberModel();
        $objNewUser->setRow($data);
        $objNewUser->save();

        // Assign home directory
        if ($this->reg_assignDir) {
            $objHomeDir = \FilesModel::findByUuid($this->reg_homeDir);

            if (null !== $objHomeDir) {
                $this->import('Files');
                $strUserDir = standardize($data['username']) ?: 'user_'.$objNewUser->id;

                // Add the user ID if the directory exists
                while (is_dir(TL_ROOT.'/'.$objHomeDir->path.'/'.$strUserDir)) {
                    $strUserDir .= '_'.$objNewUser->id;
                }

                // Create the user folder
                new \Folder($objHomeDir->path.'/'.$strUserDir);

                $objUserDir = \FilesModel::findByPath($objHomeDir->path.'/'.$strUserDir);

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
        $objVersions = new \Versions('tl_member', $objNewUser->id);
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
}
