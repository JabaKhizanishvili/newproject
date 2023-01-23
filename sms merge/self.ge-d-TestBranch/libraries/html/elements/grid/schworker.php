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
class JGridElementSchworker extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'schworker';

	public function fetchElement( $row, $node, $group )
	{
//		Working on this element ...
		$key = trim( $node->attributes( 'key' ) );
		$Offices = $this->getPerson();
		$Value = C::_( $key, $row );
		return C::_( $Value . '.TITLE', $Offices );

	}

	public function getPerson()
	{
		static $Offices = null;
		if ( is_null( $Offices ) )
		{
			$query = 'select '
							. ' id, '
							. ' t.firstname ||\' \'||  t.lastname title '
							. ' from hrs_workers_sch t ';
			$Offices = DB::LoadObjectList( $query, 'ID' );
		}
		return $Offices;

	}

}
