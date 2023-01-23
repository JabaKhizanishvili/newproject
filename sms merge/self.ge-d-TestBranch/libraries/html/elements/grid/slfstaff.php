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
class JGridElementSlfstaff extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'slfstaff';

	public function fetchElement( $row, $node, $group )
	{
//		Working on this element ...
		$key = trim( $node->attributes( 'key' ) );
		$Tscope = trim( $node->attributes( 'tscope' ) );
		$Offices = $this->getPerson();
		$Value = C::_( $key, $row );
		$Text = XTranslate::_( C::_( $Value . '.FIRSTNAME', $Offices ), $Tscope );
		$Text .= ' ' . XTranslate::_( C::_( $Value . '.LASTNAME', $Offices ), $Tscope );
		return $Text;

	}

	public function getPerson()
	{
		static $Offices = null;
		if ( is_null( $Offices ) )
		{
			$query = 'select '
							. ' id, '
							. ' t.firstname, '
							. ' t.lastname '
							. ' from slf_persons t ';
			$Offices = DB::LoadObjectList( $query, 'ID' );
		}
		return $Offices;

	}

}
