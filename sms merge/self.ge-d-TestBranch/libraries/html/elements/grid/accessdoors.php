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
class JGridElementAccessdoors extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'accessdoors';

	public function fetchElement( $row, $node, $group )
	{
		/* @var $node SimpleXMLElements */
		$key = trim( $node->attributes( 'key' ) );
		$LimitType = trim( $node->attributes( 'limit_type' ) );
		$Length = intval( $node->attributes( 'length' ) );
		$translate = trim( $node->attributes( 't' ) );
		$Text = '';
		if ( isset( $row->{$key} ) )
		{
			$doors = $this->getDoors( $row->{$key} );
			$Text = trim( stripslashes( $doors ) );
			switch ( $LimitType )
			{
				case 1:
					$Text = Helper::MakeToolTip( $Text, $Length, 0 );
					break;
				case 2:
					$Text = Helper::MakeToolTip( $Text, $Length, 1 );
					break;
			}
			if ( $translate == 1 )
			{
				$Text = XTranslate::_( $Text );
			}
			return $Text;
		}

	}

	public function getDoors( $id )
	{
		static $stop = array();
		if ( !count( $stop ) )
		{
			$query = 'SELECT
	ram.access_id,
	ld.lib_title
FROM
	REL_ACCESS_MANAGER ram
LEFT JOIN lib_doors ld ON
	ram.CONTROLLER = ld.ID 
WHERE
	ld.active = 1';

			$doors = DB::LoadObjectList( $query );
			foreach ( $doors as $door )
			{
				$access_id = C::_( 'ACCESS_ID', $door );
				$stop[$access_id] = C::_( $access_id, $stop, array() );
				$stop[$access_id][] = C::_( 'LIB_TITLE', $door );
			}
		}
		return implode( ', ', C::_( $id, $stop ) );

	}

}
