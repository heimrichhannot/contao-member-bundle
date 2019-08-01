<?php

/*
 * Copyright (c) 2019 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\MemberBundle\Form;

use Contao\Config;
use Contao\Widget;

class FormPasswordNoConfirm extends Widget
{
    protected $blnSubmitInput = true;

    protected $strTemplate = 'form_password_noConfirm';

    public function generate()
    {
        return sprintf('<input type="password" name="%s" id="ctrl_%s" class="text password%s" value=""%s%s',
                $this->strName,
                $this->strId,
                (('' !== $this->strClass) ? ' '.$this->strClass : ''),
                $this->getAttributes(),
                $this->strTagEnding).$this->addSubmit();
    }

    protected function validator($varInput)
    {
        $this->blnSubmitInput = false;

        if (!\strlen($varInput) && (\strlen($this->varValue) || !$this->mandatory)) {
            return '';
        }

        if (utf8_strlen($varInput) < Config::get('minPasswordLength')) {
            $this->addError(sprintf($GLOBALS['TL_LANG']['ERR']['passwordLength'], Config::get('minPasswordLength')));
        }

        $varInput = parent::validator($varInput);

        if (!$this->hasErrors()) {
            $this->blnSubmitInput = true;

            return \Encryption::hash($varInput);
        }

        return '';
    }
}
