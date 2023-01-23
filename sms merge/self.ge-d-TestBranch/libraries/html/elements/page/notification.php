<?php

/**
 * @version		$Id: calendar.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Framework
 * @copyright	Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
// Check to ensure this file is included in WSCMS
defined('PATH_BASE') or die('Restricted access');

/**
 * Renders a calendar element
 *
 * @package 	WSCMS.Framework
 * @subpackage	Parameter
 * @since		1.5
 */
class PageElementNotification extends PageElement
{

    /**
     * Element name
     * @access	protected
     * @var		string
     */
    protected $_name = 'Notification';

    public function fetchElement($row, $node, $group)
    {
        $key = trim($node->attributes('key'));
        if($key)
        {
            $data = json_decode(stripslashes(C::_($key, $row)));
        }
        else
        {
            return '';
        }
        if(empty($data))
        {
            return '';
        }

        $html = '<div class="formnotifyblock">';
        foreach($data as $Notify)
        {
            $html .= '<div class="formnotifyItem">'
                    . '<div class="formNotifyDate"><b>'
                    . Text::_('Date') . ':</b> '
                    . $Notify->N_DATE
                    . '</div>'
                    . '<div class="cls"></div>'
                    . '<div class="formNotifyText"><b>'
                    . Text::_('Message') . ':</b> '
                    . $Notify->N_TEXT
                    . '</div>'
                    . '<hr class="divider" />'
                    . '<div class="cls"></div>'
                    . '</div>'
                    . '<div class="cls"></div>'
            ;
        }

        return $html;
    }

}
