<?php
/**
 * @version		$Id: password.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Framework
 * @copyright	Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
// Check to ensure this file is within the rest of the framework

/**
 * Renders a password element
 *
 * @package 	WSCMS.Framework
 * @subpackage		Parameter
 * @since		1.5
 */
class JElementPassword extends JElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'Password';

	public function fetchElement( $name, $value, $node, $control_name )
	{
        return '<input type="password" name="' . $control_name .'[' . $name . ']' . '" id="' . $control_name . ' ' . $name . '" value="" class="form-control" autocomplete="off" />';
	}

}
