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
class JElementPrintAD extends JElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'PrintAD';

	public function fetchElement( $name, $valueIN, $node, $control_name )
	{
		$value = htmlspecialchars( html_entity_decode( trim( $valueIN ), ENT_QUOTES ), ENT_QUOTES );
		if ( empty( $value ) )
		{
			return '<strong style="color:red;">' . Text::_( 'ADUsername Not Defined!' ) . '</strong>';
		}
		else
		{
			return '<strong>' . $value . '</strong>';
		}

	}

}
