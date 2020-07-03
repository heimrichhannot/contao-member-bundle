<?php

/*
 * Copyright (c) 2020 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\MemberBundle\Event;

use HeimrichHannot\FormHybrid\Form;
use Symfony\Component\EventDispatcher\Event;

class MemberBeforeLoginRedirectEvent extends Event
{
    const NAME = 'huh.member.before_login_redirect';
    private string $username;
    private string $redirectUrl;
    /**
     * @var Form
     */
    private $form;

    public function __construct(string $username, string $redirectUrl, Form $form)
    {
        $this->username = $username;
        $this->redirectUrl = $redirectUrl;
        $this->form = $form;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getRedirectUrl(): string
    {
        return $this->redirectUrl;
    }

    public function setRedirectUrl(string $redirectUrl): void
    {
        $this->redirectUrl = $redirectUrl;
    }

    public function getForm(): Form
    {
        return $this->form;
    }

    public function setForm(Form $form): void
    {
        $this->form = $form;
    }
}
