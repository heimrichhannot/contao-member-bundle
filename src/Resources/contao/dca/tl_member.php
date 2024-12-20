<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

\Controller::loadLanguageFile('tl_content');
\Controller::loadDataContainer('tl_content');

$dca = &$GLOBALS['TL_DCA']['tl_member'];

/*
 * Palettes
 */
if (!class_exists('HeimrichHannot\MemberPlus\MemberPlus')) {
    // selector
    $dca['palettes']['__selector__'][] = 'addImage';

    // title
    $dca['palettes']['default'] = '{title_legend},headline;'.$dca['palettes']['default'];
    // alias - must be invoked after firstname & title, otherwise not available in save_callback
    $dca['palettes']['default'] = str_replace('lastname', 'lastname,alias', $dca['palettes']['default']);
    // titles
    $dca['palettes']['default'] = str_replace('firstname', 'academicTitle,extendedTitle,nobilityTitle,academicDegree,jobTitles,firstname', $dca['palettes']['default']);
    // personal
    $dca['palettes']['default'] = str_replace('gender', 'gender,position', $dca['palettes']['default']);
    // address
    $dca['palettes']['default'] = str_replace('country', 'country,addressText,additionalAddresses', $dca['palettes']['default']);
    $dca['palettes']['default'] = str_replace('street,', 'street,street2,', $dca['palettes']['default']);
    // image
    $dca['palettes']['default'] = str_replace('assignDir', 'assignDir;{image_legend},addImage;', $dca['palettes']['default']);
    // contact
    $dca['palettes']['default'] = str_replace('website', 'website,xingProfile,linkedinProfile,facebookProfile,twitterProfile,googlePlusProfile', $dca['palettes']['default']);
    $dca['palettes']['default'] = str_replace('language', 'language,foreignLanguages', $dca['palettes']['default']);
}

/*
 * Subpalettes
 */

$dca['subpalettes']['addImage'] = 'singleSRC,alt,imageTitle,size,imagemargin,imageUrl,fullsize,caption,floating';

if (TL_MODE == 'FE') {
    unset($dca['palettes']['__selector__']);
}

$fields = [
    'headline' => [
        'label' => &$GLOBALS['TL_LANG']['tl_member']['headline'],
        'exclude' => true,
        'filter' => true,
        'sorting' => true,
        'inputType' => 'text',
        'eval' => ['feEditable' => true, 'feViewable' => true, 'feGroup' => 'title', 'tl_class' => 'w50'],
        'sql' => "varchar(255) NOT NULL default ''",
    ],
    'alias' => [
        'label' => &$GLOBALS['TL_LANG']['tl_member']['alias'],
        'exclude' => true,
        'search' => true,
        'inputType' => 'text',
        'eval' => [
            'rgxp' => 'alias',
            'unique' => true,
            'spaceToUnderscore' => true,
            'maxlength' => 128,
            'tl_class' => 'w50',
            'doNotCopy' => true,
        ],
        'save_callback' => [
            ['huh.member.backend.member', 'generateAlias'],
        ],
        'sql' => "varbinary(128) NOT NULL default ''",
    ],
    'academicTitle' => [
        'label' => &$GLOBALS['TL_LANG']['tl_member']['academicTitle'],
        'exclude' => true,
        'filter' => true,
        'sorting' => true,
        'inputType' => 'text',
        'eval' => ['feEditable' => true, 'feViewable' => true, 'feGroup' => 'personal', 'tl_class' => 'w50'],
        'sql' => "varchar(255) NOT NULL default ''",
    ],
    'academicDegree' => [
        'label' => &$GLOBALS['TL_LANG']['tl_member']['academicDegree'],
        'exclude' => true,
        'filter' => true,
        'sorting' => true,
        'inputType' => 'text',
        'eval' => ['feEditable' => true, 'feViewable' => true, 'feGroup' => 'personal', 'tl_class' => 'w50'],
        'sql' => "varchar(255) NOT NULL default ''",
    ],
    'extendedTitle' => [
        'label' => &$GLOBALS['TL_LANG']['tl_member']['extendedTitle'],
        'exclude' => true,
        'filter' => true,
        'sorting' => true,
        'inputType' => 'text',
        'eval' => ['feEditable' => true, 'feViewable' => true, 'feGroup' => 'personal', 'tl_class' => 'w50'],
        'sql' => "varchar(255) NOT NULL default ''",
    ],
    'nobilityTitle' => [
        'label' => &$GLOBALS['TL_LANG']['tl_member']['nobilityTitle'],
        'exclude' => true,
        'filter' => true,
        'sorting' => true,
        'inputType' => 'text',
        'eval' => ['feEditable' => true, 'feViewable' => true, 'feGroup' => 'personal', 'tl_class' => 'w50'],
        'sql' => "varchar(255) NOT NULL default ''",
    ],
    'jobTitles' => [
        'label' => &$GLOBALS['TL_LANG']['tl_member']['jobTitles'],
        'exclude' => true,
        'filter' => true,
        'sorting' => true,
        'options_callback' => ['huh.member.backend.member', 'getJobTitleChoices'],
        'inputType' => 'tagsinput',
        'eval' => ['feEditable' => true, 'feViewable' => true, 'feGroup' => 'personal', 'tl_class' => 'w50', 'freeInput' => true, 'multiple' => true],
        'sql' => 'blob NULL',
    ],
    'position' => [
        'label' => &$GLOBALS['TL_LANG']['tl_member']['position'],
        'exclude' => true,
        'filter' => true,
        'sorting' => true,
        'inputType' => 'text',
        'eval' => ['feEditable' => true, 'feViewable' => true, 'feGroup' => 'personal', 'tl_class' => 'w50'],
        'sql' => "varchar(255) NOT NULL default ''",
    ],
    'street2' => [
        'label' => &$GLOBALS['TL_LANG']['tl_member']['street2'],
        'exclude' => true,
        'search' => true,
        'inputType' => 'text',
        'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
        'sql' => "varchar(255) NOT NULL default ''",
    ],
    'addressText' => [
        'label' => &$GLOBALS['TL_LANG']['tl_member']['addressText'],
        'exclude' => true,
        'search' => true,
        'inputType' => 'textarea',
        'eval' => [
            'feEditable' => true,
            'feViewable' => true,
            'feGroup' => 'address',
            'rte' => 'tinyMCE',
            'tl_class' => 'clr',
            'helpwizard' => true,
        ],
        'explanation' => 'insertTags',
        'sql' => 'mediumtext NULL',
    ],
    'addImage' => [
        'label' => &$GLOBALS['TL_LANG']['tl_member']['addImage'],
        'exclude' => true,
        'inputType' => 'checkbox',
        'eval' => ['submitOnChange' => true],
        'sql' => "char(1) NOT NULL default ''",
    ],
    'singleSRC' => [
        'label' => &$GLOBALS['TL_LANG']['tl_content']['singleSRC'],
        'exclude' => true,
        'inputType' => 'fileTree',
        'eval' => ['filesOnly' => true, 'fieldType' => 'radio', 'mandatory' => true, 'tl_class' => 'clr'],
        'load_callback' => [
            ['tl_content', 'setSingleSrcFlags'],
        ],
        'sql' => 'binary(16) NULL',
    ],
    'alt' => [
        'label' => &$GLOBALS['TL_LANG']['tl_content']['alt'],
        'exclude' => true,
        'search' => true,
        'inputType' => 'text',
        'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
        'sql' => "varchar(255) NOT NULL default ''",
    ],
    'imageTitle' => [
        'label' => &$GLOBALS['TL_LANG']['tl_content']['imageTitle'],
        'exclude' => true,
        'search' => true,
        'inputType' => 'text',
        'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
        'sql' => "varchar(255) NOT NULL default ''",
    ],
    'size' => [
        'label' => &$GLOBALS['TL_LANG']['tl_content']['size'],
        'exclude' => true,
        'inputType' => 'imageSize',
        'reference' => &$GLOBALS['TL_LANG']['MSC'],
        'eval' => ['rgxp' => 'natural', 'includeBlankOption' => true, 'nospace' => true, 'helpwizard' => true, 'tl_class' => 'w50'],
        'options_callback' => static function () {
            return Contao\System::getContainer()->get('contao.image.image_sizes')->getOptionsForUser(Contao\BackendUser::getInstance());
        },
        'sql' => "varchar(255) NOT NULL default ''",
    ],
    'imagemargin' => [
        'label' => &$GLOBALS['TL_LANG']['tl_content']['imagemargin'],
        'exclude' => true,
        'inputType' => 'trbl',
        'options' => $GLOBALS['TL_CSS_UNITS'],
        'eval' => ['includeBlankOption' => true, 'tl_class' => 'w50'],
        'sql' => "varchar(128) NOT NULL default ''",
    ],
    'imageUrl' => [
        'label' => &$GLOBALS['TL_LANG']['tl_content']['imageUrl'],
        'exclude' => true,
        'search' => true,
        'inputType' => 'text',
        'eval' => ['rgxp' => 'url', 'decodeEntities' => true, 'dcaPicker' => true, 'addWizardClass' => false, 'tl_class' => 'w50'],
        'sql' => "TEXT NULL",
    ],
    'fullsize' => [
        'label' => &$GLOBALS['TL_LANG']['tl_content']['fullsize'],
        'exclude' => true,
        'inputType' => 'checkbox',
        'eval' => ['tl_class' => 'w50 m12'],
        'sql' => "char(1) NOT NULL default ''",
    ],
    'caption' => [
        'label' => &$GLOBALS['TL_LANG']['tl_content']['caption'],
        'exclude' => true,
        'search' => true,
        'inputType' => 'text',
        'eval' => ['maxlength' => 255, 'allowHtml' => true, 'tl_class' => 'w50'],
        'sql' => "varchar(255) NOT NULL default ''",
    ],
    'floating' => [
        'label' => &$GLOBALS['TL_LANG']['tl_content']['floating'],
        'exclude' => true,
        'inputType' => 'radioTable',
        'options' => ['above', 'left', 'right', 'below'],
        'eval' => ['cols' => 4, 'tl_class' => 'w50'],
        'reference' => &$GLOBALS['TL_LANG']['MSC'],
        'sql' => "varchar(32) NOT NULL default 'above'",
    ],
    'captcha' => [
        'label' => &$GLOBALS['TL_LANG']['MSC']['securityQuestion'],
        'exclude' => true,
        'inputType' => 'captcha',
    ],
    'linkedinProfile' => [
        'label' => &$GLOBALS['TL_LANG']['tl_member']['linkedinProfile'],
        'exclude' => true,
        'search' => true,
        'inputType' => 'text',
        'save_callback' => [['huh.member.backend.member', 'addURIScheme']],
        'eval' => ['rgxp' => 'url', 'maxlength' => 255, 'tl_class' => 'w50'],
        'sql' => "varchar(255) NOT NULL default ''",
    ],
    'xingProfile' => [
        'label' => &$GLOBALS['TL_LANG']['tl_member']['xingProfile'],
        'exclude' => true,
        'search' => true,
        'save_callback' => [['huh.member.backend.member', 'addURIScheme']],
        'inputType' => 'text',
        'eval' => ['rgxp' => 'url', 'maxlength' => 255, 'tl_class' => 'w50'],
        'sql' => "varchar(255) NOT NULL default ''",
    ],
    'facebookProfile' => [
        'label' => &$GLOBALS['TL_LANG']['tl_member']['facebookProfile'],
        'exclude' => true,
        'search' => true,
        'save_callback' => [['huh.member.backend.member', 'addURIScheme']],
        'inputType' => 'text',
        'eval' => ['rgxp' => 'url', 'maxlength' => 255, 'tl_class' => 'w50'],
        'sql' => "varchar(255) NOT NULL default ''",
    ],
    'twitterProfile' => [
        'label' => &$GLOBALS['TL_LANG']['tl_member']['twitterProfile'],
        'exclude' => true,
        'search' => true,
        'save_callback' => [['huh.member.backend.member', 'addURIScheme']],
        'inputType' => 'text',
        'eval' => ['rgxp' => 'url', 'maxlength' => 255, 'tl_class' => 'w50'],
        'sql' => "varchar(255) NOT NULL default ''",
    ],
    'googlePlusProfile' => [
        'label' => &$GLOBALS['TL_LANG']['tl_member']['googlePlusProfile'],
        'exclude' => true,
        'search' => true,
        'save_callback' => [['huh.member.backend.member', 'addURIScheme']],
        'inputType' => 'text',
        'eval' => ['rgxp' => 'url', 'maxlength' => 255, 'tl_class' => 'w50'],
        'sql' => "varchar(255) NOT NULL default ''",
    ],
    'foreignLanguages' => [
        'label' => &$GLOBALS['TL_LANG']['tl_member']['foreignLanguages'],
        'exclude' => true,
        'filter' => true,
        'inputType' => 'tagsinput',
        'options' => System::getLanguages(),
        'eval' => ['freeInput' => false, 'multiple' => true, 'includeBlankOption' => true, 'chosen' => true, 'rgxp' => 'locale', 'feEditable' => true, 'feViewable' => true, 'feGroup' => 'personal', 'tl_class' => 'w50 autoheight'],
        'sql' => 'blob NULL',
    ],
    'additionalAddresses' => [
        'label' => &$GLOBALS['TL_LANG']['tl_member']['additionalAddresses'],
        'inputType' => 'fieldpalette',
        'exclude' => true,
        'foreignKey' => 'tl_member_address.id',
        'relation' => ['type' => 'hasMany', 'load' => 'eager'],
        'eval' => ['tl_class' => 'clr'],
        'sql' => 'blob NULL',
        'fieldpalette' => [
            'config' => [
                'hidePublished' => false,
                'table' => 'tl_member_address',
            ],
            'list' => [
                'label' => [
                    'fields' => ['city'],
                    'format' => '%s',
                ],
            ],
            'palettes' => [
                'default' => '{contact_legend},phone,fax;{address_legend},company,street,street2,postal,city,state,country,addressText',
            ],
        ],
    ],
];

$dca['fields'] = array_merge($dca['fields'], $fields);

if (System::getContainer()->get('huh.utils.container')->isBackend()) {
    $dca['fields']['email']['eval']['mandatory'] = false;
}

if (System::getContainer()->get('huh.utils.container')->isFrontend()) {
    $dca['fields']['gender']['inputType'] = 'radio';
    $dca['fields']['gender']['eval']['includeBlankOption'] = false;
}

// increase activation field, otherwise MEMBER_ACTIVATION_ACTIVATED_FIELD_PREFIX will not fit in
$dca['fields']['activation']['sql'] = "varchar(64) NOT NULL default ''";
