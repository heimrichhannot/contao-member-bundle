<?php
/**
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['parseListItem'][] = ['huh.member.hooks', 'switchAddress'];

/**
 * Modules
 */
$GLOBALS['FE_MOD']['user']['registration_plus'] = '\HeimrichHannot\MemberBundle\Module\ModuleRegistrationPlus';

/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_registration_plus'] = '\HeimrichHannot\MemberBundle\Form\MemberRegistrationPlusForm';

/**
 * Constants
 */
define('MEMBER_ACTIVATION_ACTIVATED_FIELD_PREFIX', 'ACTIVATED:');