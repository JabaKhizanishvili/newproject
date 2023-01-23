<?php
/**
 * @version		$Id: Print.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Framework
 * @copyright	Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
// Check to ensure this file is within the rest of the framework

/**
 * Renders a Print element
 *
 * @package 	WSCMS.Framework
 * @subpackage		Parameter
 * @since		1.5
 */
class JElementOrgPrint extends JElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'Print';

	public function fetchElement( $name, $valueIN, $node, $control_name )
	{
		$Orgs = Units::getOrgList();
		$value = htmlspecialchars( html_entity_decode( $valueIN, ENT_QUOTES ), ENT_QUOTES );
		if ( !$value )
		{
			$value = (int) trim( Request::getState( '.display', 'org', '' ) );
		}
		echo '<input type="hidden" name="params[' . $name . ']" id="params' . $name . '" value="' . $valueIN . '">';
		return '<div class="form-control"><strong>' . XTranslate::_( C::_( $value . '.TITLE', $Orgs ) ) . '</strong></div>';

	}

}
