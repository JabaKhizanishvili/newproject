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
class PageElementChiefs extends PageElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'Chiefs ';

	public function fetchElement( $row, $node, $group )
	{
		$Key = $node->attributes( 'key' );
		$Value = trim( C::_( $Key, $row ) );
		if ( empty( $Value ) )
		{
			return '';
		}
		$Data = $this->LoadData( $Value );
		return $Data;

	}

	public function LoadData( $Value )
	{
		$Key = md5( $Value );
		static $Data = array();
		if ( !isset( $Data[$Key] ) )
		{
			$query = 'select '
							. ' t.firstname || \' \' || t.lastname v_name '
							. ' from slf_persons t '
							. ' where '
							. ' t.id in (' . $Value . ')';
			$Data[$Key] = implode( ', ', DB::LoadList( $query ) );
		}
		return $Data[$Key];

	}

}
