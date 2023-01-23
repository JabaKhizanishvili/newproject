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
class JGridElementUTime extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'UTime';

	public function fetchElement( $row, $node, $group )
	{
		$key = trim( $node->attributes( 'key' ) );
		$ID = C::_( $key, $row );
		$on_off = (int) Helper::getConfig( 'private_date' );
		$ptime_orgs = (array) explode( '|', Helper::getConfig( 'private_date_orgs' ) );
		$check_org = (int) C::_( 'ORG', XGraph::GetOrgUser( $ID ) );
		if ( in_array( $check_org, $ptime_orgs ) && $ID && $on_off == 1 )
		{
			$AllMinutes = Helper::getRemPrivateTime( $ID );
			$Hours = floor( $AllMinutes / 60 );
			$Minute = ($AllMinutes % 60);
			return $Hours . ' ' . Text::_( 'Hour' ) . ', ' . $Minute . ' ' . Text::_( 'minute' );
		}

		return false;

	}

}
