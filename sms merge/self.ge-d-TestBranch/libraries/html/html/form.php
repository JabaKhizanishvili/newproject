<?php

/**
 * @version		$Id: form.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Framework
 * @copyright	Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */

/**
 * Utility class for form elements
 *
 * @static
 * @package 	WSCMS.Framework
 * @subpackage	HTML
 * @version		1.5
 */
class HTMLForm
{

    /**
     * Displays a hidden token field to reduce the risk of CSRF exploits
     *
     * Use in conjuction with JRequest::checkToken
     *
     * @static
     * @return	string
     * @since	1.5
     */
    public static function token()
    {
        return '<input type="hidden" name="' . JUtility::getToken() . '" value="1" />';
    }

}
