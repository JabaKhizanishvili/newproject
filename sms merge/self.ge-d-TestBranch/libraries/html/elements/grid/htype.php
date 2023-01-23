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
class JGridElementhtype extends JGridElement
{
	public function fetchElement( $row, $node, $group )
	{
		$key = trim( $node->attributes( 'key' ) );
		$Value = C::_( $key, $row, null );
		if ( !is_null( $Value ) )
		{
			if ( $Value == 0 || $Value == 3 )
			{
				return Text::_( 'wages' );
			}
			else
			{
				return Text::_( 'wageless' );
			}
		}

	}

}
