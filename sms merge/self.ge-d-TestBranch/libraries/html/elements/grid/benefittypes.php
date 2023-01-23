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
class JGridElementBenefittypes extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'benefittypes';

	public function fetchElement( $row, $node, $group )
	{
		$return = '';
		$key = trim( $node->attributes( 'key' ) );
		$Offices = $this->getBenList();
		if ( !count( $Offices ) )
		{
			return 'Undefined';
		}

		$Value = explode( ',', C::_( $key, $row ) );
		if ( is_array( $Value ) )
		{
			$collect = [];
			foreach ( $Value as $id )
			{
				if ( $id )
				{
					$office = C::_( $id, $Offices );
					$collect[] = C::_( 'LIB_TITLE', $office );
				}
			}
			$return = implode( ', ', $collect );
		}
		else
		{
			$office = C::_( $Value, $Offices );
			$return = C::_( 'LIB_TITLE', $office );
		}
		return $return;

	}

	public function getBenList()
	{
		static $Data = null;
		if ( is_null( $Data ) )
		{
			$query = 'select '
							. ' t.id, '
							. ' t.lib_title, '
							. ' t.lib_desc '
							. ' from lib_f_benefit_types t '
							. ' where t.id >0 '
							. ' order by t.lib_title ';
			$Data = DB::LoadObjectList( $query, 'ID' );
		}
		return $Data;

	}

}
