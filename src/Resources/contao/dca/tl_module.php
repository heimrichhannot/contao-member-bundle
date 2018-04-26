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

/**
 * Subpalettes
 */
$dc['subpalettes']['reg_activate_plus'] = 'formHybridConfirmationMailRecipientField,formHybridConfirmationAvisotaMessage,formHybridConfirmationMailSender,formHybridConfirmationMailSubject,formHybridConfirmationMailText,formHybridConfirmationMailTemplate,formHybridConfirmationMailAttachment';

/**
 * Callbacks
 */
$dc['config']['onload_callback'][] = ['tl_module_member_plus', 'modifyPalette'];

/**
 * Fields
 */
$arrFields = [
    'reg_activate_plus' => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['reg_activate'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['submitOnChange' => true, 'tl_class' => 'w50 clr'],
        'sql'       => "char(1) NOT NULL default ''",
    ],
];

$dc['fields'] = array_merge($dc['fields'], $arrFields);

class tl_module_member_plus extends \Backend
{

    /**
     * Return all news templates as array
     *
     * @return array
     */
    public function getMemberlistTemplates()
    {
        return $this->getTemplateGroup('memberlist_');
    }

    public function modifyPalette()
    {
        $objModule = \ModuleModel::findByPk(\Input::get('id'));
        $arrDc     = &$GLOBALS['TL_DCA']['tl_module'];

        // submission -> already done in formhybrid

        // confirmation
        $arrFieldsToHide = [
            'formHybridConfirmationMailSender',
            'formHybridConfirmationMailSubject',
            'formHybridConfirmationMailText',
            'formHybridConfirmationMailTemplate',
            'formHybridConfirmationMailAttachment',
        ];

        if (in_array('avisota-core', \ModuleLoader::getActive()) && in_array('formhybrid', \ModuleLoader::getActive()) && $objModule->reg_activate_plus && $objModule->formHybridConfirmationAvisotaMessage) {
            $arrDc['subpalettes']['reg_activate_plus'] = str_replace($arrFieldsToHide, array_map(function () {
                return '';
            }, $arrFieldsToHide), $arrDc['subpalettes']['reg_activate_plus']);

            $arrDc['subpalettes']['reg_activate_plus'] = str_replace('formHybridConfirmationAvisotaMessage', 'formHybridConfirmationAvisotaMessage,formHybridConfirmationAvisotaSalutationGroup', $arrDc['subpalettes']['reg_activate_plus']);
        }
    }

    /**
     * Return all feViewable fields as array
     *
     * @param DataContainer $dc
     *
     * @return array
     */
    public function getViewableMemberFields(DataContainer $dc)
    {
        \Controller::loadDataContainer('tl_member');
        \Controller::loadLanguageFile('tl_member');

        $arrOptions = [];

        $arrFields = $GLOBALS['TL_DCA']['tl_member']['fields'];

        if (!is_array($arrFields) || empty($arrFields)) {
            return $arrOptions;
        }

        foreach ($arrFields as $strName => $arrData) {
            if (!isset($arrData['inputType'])) {
                continue;
            }

            if (!$arrData['eval']['feViewable']) {
                continue;
            }

            $arrOptions[$strName] = $arrData['label'][0];
        }

        return $arrOptions;
    }
}
