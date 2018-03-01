<?php
/**
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

$arrDca = &$GLOBALS['TL_DCA']['tl_member'];

// add multiple addresses
$arrDca['fields']['additionalAddresses'] = [
    'label'        => &$GLOBALS['TL_LANG']['tl_member']['additionalAddresses'],
    'inputType'    => 'fieldpalette',
    'foreignKey'   => 'tl_member_address.id',
    'relation'     => ['type' => 'hasMany', 'load' => 'eager'],
    'sql'          => "blob NULL",
    'fieldpalette' => [
        'config'   => [
            'hidePublished' => false,
            'table'         => 'tl_member_address',
        ],
        'list'     => [
            'label' => [
                'fields' => ['city'],
                'format' => '%s',
            ],
        ],
        'palettes' => [
            'default' => '{contact_legend},phone,fax;{address_legend},company,street,street2,postal,city,state,country,addressText',
        ],
    ],
];