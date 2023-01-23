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
class JGridElementhstatus extends JGridElement
{
	public function fetchElement( $row, $node, $group )
	{
		$key = trim( $node->attributes( 'key' ) );
		$Value = C::_( $key, $row, null );
		switch ( $Value )
		{
			case '0':
			case '1':
				echo '<img src="templates/images/holiday.png" alt="" height="17" />';
				break;
			case '5':
				echo '<img src="templates/images/ambulance.png" alt=""  height="17" />';
				break;
			default:
				break;
		}

	}

}
