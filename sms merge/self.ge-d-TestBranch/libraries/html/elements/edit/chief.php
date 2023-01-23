<?php

/**
 * @version		$Id: list.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Framework
 * @copyright	Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
// Check to ensure this file is within the rest of the framework
/**
 * Renders a list element
 *
 * @package 	WSCMS.Framework
 * @subpackage		Parameter
 * @since		1.5
 */
class JElementChief extends JElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'Chief';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$User = Users::getUser( (int) $value );
		$Text = C::_( 'FIRSTNAME', $User ) . ' ' . C::_( 'LASTNAME', $User );
		$HTML = '<input type="text" class="form-control-static form-control" readonly value="' . $Text . '" />'
						. '<input type="hidden" name="' . $control_name . '[' . $name . ']' . '" id="' . $control_name . $name . '" value="' . $value . '"  /> ';
		return$HTML;

	}

}