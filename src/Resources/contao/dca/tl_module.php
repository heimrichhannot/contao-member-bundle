<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2015 Heimrich & Hannot GmbH
 *
 * @package member_plus
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

$dc = &$GLOBALS['TL_DCA']['tl_module'];

/**
 * Palettes
 */
array_insert($dc['palettes']['__selector__'], 0, ['reg_activate_plus']); // bug? mustn't be inserted after type selector

$dc['palettes']['registration_plus'] = '{title_legend},name,headline,type;
	{config_legend},formHybridDataContainer,formHybridPalette,formHybridEditable,formHybridAddEditableRequired,formHybridTemplate,formHybridCustomSubTemplates,formHybridAsync,formHybridCssClass,formHybridAddDefaultValues,disableCaptcha,newsletters;
	{account_legend},reg_groups,reg_allowLogin;
	{message_legend},formHybridSuccessMessage;
	{email_legend:hide},reg_jumpTo,formHybridSendConfirmationAsNotification,reg_activate_plus,formHybridSendSubmissionAsNotification,formHybridSendSubmissionViaEmail;
	{template_legend:hide},customTpl;{redirect_legend},jumpTo,
	{protected_legend:hide},protected;
	{expert_legend:hide},guests';

$dc['palettes']['login_registration'] =
    '{title_legend},name,headline,type;
	{config_legend},formHybridDataContainer,formHybridPalette,formHybridEditable,formHybridAddEditableRequired,formHybridTemplate,formHybridCustomSubTemplates,formHybridCssClass,formHybridAddDefaultValues,disableCaptcha,bypassPasswordConfirm,newsletters;
	{account_legend},reg_groups,reg_allowLogin;
	{email_legend:hide},reg_jumpTo,formHybridSendConfirmationAsNotification,formHybridSendSubmissionAsNotification,formHybridSendSubmissionViaEmail;
	{template_legend:hide},customTpl;{redirect_legend},jumpTo, redirectLogin;
	{protected_legend:hide},protected;
	{expert_legend:hide},guests';

/**
 * Subpalettes
 */
$dc['subpalettes']['reg_activate_plus'] = 'formHybridConfirmationMailRecipientField,formHybridConfirmationAvisotaMessage,formHybridConfirmationMailSender,formHybridConfirmationMailSubject,formHybridConfirmationMailText,formHybridConfirmationMailTemplate,formHybridConfirmationMailAttachment';

/**
 * Callbacks
 */

/**
 * Fields
 */
$arrFields = [
    'reg_activate_plus'  => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['reg_activate'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['submitOnChange' => true, 'tl_class' => 'w50 clr'],
        'sql'       => "char(1) NOT NULL default ''",
    ],
    'bypassPasswordConfirm' => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['bypassPasswordConfirm'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50 clr'],
        'sql'       => "char(1) NOT NULL default ''"
    ],
    'redirectLogin'  => [
    'label'      => &$GLOBALS['TL_LANG']['tl_module']['redirectLogin'],
    'exclude'    => true,
    'inputType'  => 'pageTree',
    'foreignKey' => 'tl_page.title',
    'eval'       => ['fieldType' => 'radio', 'tl_class' => 'w50 clr'],
    'sql'        => "int(10) unsigned NOT NULL default '0'",
    'relation'   => ['type' => 'hasOne', 'load' => 'lazy']
    ]
];



$dc['fields'] = array_merge($dc['fields'], $arrFields);