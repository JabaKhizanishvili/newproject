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
class JGridElementFields extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'fields';

	public function fetchElement( $row, $node, $group )
	{
		/* @var $node SimpleXMLElements */
		$key = trim( $node->attributes( 'key' ) );
		$value = explode( ',', trim( stripslashes( $row->{$key} ) ) );
		$HTML = [];
		foreach ( $value as $value )
		{
			$HTML[] = '<div class="key_div">'
							. '<span class="key_val">'
							. Text::_( $value )
							. '</span>'
							. '</div>'
			;
		}
		$Text = '<div class="key_row">' . implode( '', $HTML ) . '</div>';
		return $Text;

	}

}
