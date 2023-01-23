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
class JGridElementWHoliday extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'WHoliday';

	public function fetchElement( $row, $node, $group )
	{
		$key = trim( $node->attributes( 'key' ) );
		$ID = C::_( $key, $row );
		if ( $ID )
		{
			$Count = Helper::getWageCount( $ID );
			return $Count . ' ' . Text::_( 'day' );
		}

	}

}
