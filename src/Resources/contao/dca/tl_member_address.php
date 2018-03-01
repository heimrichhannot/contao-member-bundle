<?php
/**
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

\Controller::loadLanguageFile('tl_fieldpalette');
\Controller::loadDataContainer('tl_fieldpalette');
\Controller::loadDataContainer('tl_member');

$GLOBALS['TL_DCA']['tl_member_address'] = $GLOBALS['TL_DCA']['tl_fieldpalette'];
$dca                                    = &$GLOBALS['TL_DCA']['tl_member_address'];

$fields = [
    'company'     => $GLOBALS['TL_DCA']['tl_member']['fields']['company'],
    'phone'       => $GLOBALS['TL_DCA']['tl_member']['fields']['phone'],
    'fax'         => $GLOBALS['TL_DCA']['tl_member']['fields']['fax'],
    'street'      => $GLOBALS['TL_DCA']['tl_member']['fields']['street'],
    'street2'     => $GLOBALS['TL_DCA']['tl_member']['fields']['street2'],
    'postal'      => $GLOBALS['TL_DCA']['tl_member']['fields']['postal'],
    'city'        => $GLOBALS['TL_DCA']['tl_member']['fields']['city'],
    'state'       => $GLOBALS['TL_DCA']['tl_member']['fields']['state'],
    'country'     => $GLOBALS['TL_DCA']['tl_member']['fields']['country'],
    'addressText' => $GLOBALS['TL_DCA']['tl_member']['fields']['addressText'],
];

$dca['fields'] = array_merge($dca['fields'], $fields);