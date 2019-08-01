<?php

/*
 * Copyright (c) 2019 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\MemberBundle\Manager;

use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Contao\Message;
use Contao\System;

class MemberMessageManager extends Message
{
    const MEMBER_MESSAGE_DANGER = 'MEMBER_DANGER';
    const MEMBER_MESSAGE_WARNING = 'MEMBER_WARNING';
    const MEMBER_MESSAGE_INFO = 'MEMBER_INFO';
    const MEMBER_MESSAGE_SUCCESS = 'MEMBER_SUCCESS';
    const MEMBER_MESSAGE_RAW = 'MEMBER_RAW';

    /**
     * @var ContaoFrameworkInterface
     */
    protected $framework;

    public function __construct(ContaoFrameworkInterface $framework)
    {
        $this->framework = $framework;
    }

    /**
     * Add an danger message.
     *
     * @param string $message The danger message
     */
    public static function addDanger(string $message)
    {
        static::add($message, static::MEMBER_MESSAGE_DANGER);
    }

    /**
     * Add a warning message.
     *
     * @param string $message The warning message
     */
    public static function addWarning(string $message)
    {
        static::add($message, static::MEMBER_MESSAGE_WARNING);
    }

    /**
     * Add a info message.
     *
     * @param string $message The info message
     */
    public static function addInfo($message, $strScope = TL_MODE)
    {
        static::add($message, static::MEMBER_MESSAGE_INFO);
    }

    /**
     * Add an success message.
     *
     * @param string $message The success message
     */
    public static function addSuccess(string $message)
    {
        static::add($message, static::MEMBER_MESSAGE_SUCCESS);
    }

    /**
     * Add a preformatted message.
     *
     * @param string $message The preformatted message
     */
    public static function addRaw($message, $strScope = TL_MODE)
    {
        static::add($message, 'TL_RAW');
    }

    /**
     * Return all messages as HTML.
     *
     * @param bool $scLayout  If true, the line breaks are different
     * @param bool $noWrapper If true, there will be no wrapping DIV
     *
     * @return string The messages HTML markup
     */
    public static function generate($scLayout = false, bool $noWrapper = false)
    {
        $strMessages = '';

        // Regular messages
        foreach (static::getTypes() as $strType) {
            if (!\is_array($_SESSION[$strType])) {
                continue;
            }

            $strClass = strtolower(preg_replace('/member_/i', '', $strType));
            $_SESSION[$strType] = array_unique($_SESSION[$strType]);

            foreach ($_SESSION[$strType] as $strMessage) {
                if ($strType === static::MEMBER_MESSAGE_RAW) {
                    $strMessages .= $strMessage;
                } else {
                    $strMessages .= sprintf('<p class="alert alert-%s">%s</p>%s', $strClass, $strMessage, "\n");
                }

                unset($_SESSION[$strType]);
            }

            if (!$_POST) {
                $_SESSION[$strType] = [];
            }
        }

        $strMessages = trim($strMessages);

        // Wrapping container
        if (!$noWrapper && '' !== $strMessages) {
            $strMessages = sprintf('%s<div class="member_message">%s%s%s</div>%s', ($scLayout ? "\n\n" : "\n"), "\n", $strMessages, "\n", ($scLayout ? '' : "\n"));
        }

        return $strMessages;
    }

    /**
     * Clear all messages, or declared only.
     *
     * @param array $types containing message valid types from getTypes that should be unset
     */
    public static function clearMessages(array $types = [])
    {
        $types = array_intersect(static::getTypes(), $types);
        foreach (static::getTypes() as $strType) {
            if (!empty($types) && \in_array($strType, $types, true)) {
                unset($_SESSION[$strType]);
                continue;
            }

            unset($_SESSION[$strType]);
        }
    }

    /**
     * Check if messages are present.
     *
     * @return bool true if messages are present, otherwise false
     */
    public static function hasMessages($strScope = TL_MODE)
    {
        foreach (static::getTypes() as $strType) {
            if (System::getContainer()->get('session')->getFlashBag()->has(static::getFlashBagKey($strType))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return all available message types.
     *
     * @return array An array of message types
     */
    public static function getTypes()
    {
        return [static::MEMBER_MESSAGE_DANGER, static::MEMBER_MESSAGE_WARNING, static::MEMBER_MESSAGE_INFO, static::MEMBER_MESSAGE_SUCCESS, static::MEMBER_MESSAGE_RAW];
    }
}
