<?php
/**
 * @version		$Id: radio.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Framework
 * @copyright	Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
// Check to ensure this file is within the rest of the framework

/**
 * Renders a radio element
 *
 * @package 	WSCMS.Framework
 * @subpackage		Parameter
 * @since		1.5
 */
class JElementPSwitch extends JElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'PSwitch';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		foreach ( $node->children() as $option )
		{
			$val = $option->attributes( 'value' );
			if ( $value == $val )
			{
				return '<div class="form-control form_field"><strong>' . Text::_( $option->data() ) . '</strong></div>';
			}
		}
		return '';

	}

}
