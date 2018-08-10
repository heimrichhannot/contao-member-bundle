<?php
/**
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['parseListItem']['huh.member.switchAddress'] = ['huh.member.listener.hooks', 'switchAddress'];

/**
 * Modules
 */
$GLOBALS['FE_MOD']['user']['registration_plus'] = '\HeimrichHannot\MemberBundle\Module\ModuleRegistrationPlus';
$GLOBALS['FE_MOD']['user']['login_registration'] = '\HeimrichHannot\MemberBundle\Module\ModuleLoginRegistration';

/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_member_address'] = '\HeimrichHannot\MemberBundle\Model\MemberAddressModel';
$GLOBALS['TL_MODELS']['tl_member']         = '\HeimrichHannot\MemberBundle\Model\MemberPlusModel';

/**
 * Constants
 */
define('MEMBER_ACTIVATION_ACTIVATED_FIELD_PREFIX', 'ACTIVATED:');

/**
 * Front end form fields
 */
$GLOBALS['TL_FFL']['passwordNoConfirm'] = 'HeimrichHannot\MemberBundle\Form\FormPasswordNoConfirm';