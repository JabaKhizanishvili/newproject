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
class JGridElementSalarydatatype extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'salarydatatype';

	public function fetchElement( $row, $node, $config )
	{
		$key = trim( $node->attributes( 'key' ) );
		$Value = (array) explode( '|', C::_( $key, $row ) );

		$Text = [];
		foreach ( $node->children() as $option )
		{
			$val = $option->attributes( 'value' );
			if ( in_array( $val, $Value ) )
			{
				$Text[] = '<div class="key_div">'
								. '<span class="key_val">'
								. Text::_( $option->data() )
								. '</span>'
								. '</div>';
			}
		}

		return '<div class="key_row">' . implode( '', $Text ) . '</div>';

	}

}
