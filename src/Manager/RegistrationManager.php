<?php

/*
 * Copyright (c) 2020 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\MemberBundle\Manager;

use Contao\Controller;
use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\MemberModel;
use Contao\Model;
use Contao\System;
use Psr\Log\LogLevel;

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
        if (isset($GLOBALS['TL_HOOKS']['activateAccount']) && \is_array($GLOBALS['TL_HOOKS']['activateAccount'])) {
            foreach ($GLOBALS['TL_HOOKS']['activateAccount'] as $callback) {
//                $objCallback = System::importStatic($callback[0]);
//                $return = $objCallback->{$callback[1]}($image, $watermark, $position, $target);
                $objCallback = System::importStatic($callback[0]);
                $objCallback->{$callback[1]}($objMember, $this);
            }
        }

        // Log activity
        System::getContainer()->get('monolog.logger.contao')->log(
            LogLevel::INFO,
            'User account ID '.$objMember->id.' ('.$objMember->email.') has been activated',
            ['contao' => new ContaoContext(__METHOD__, LogLevel::INFO)]);
        // Redirect to the jumpTo page

        System::getContainer()->get('huh.member.manager.message')->addSuccess($this->accountActivatedMessage);
    }
}
