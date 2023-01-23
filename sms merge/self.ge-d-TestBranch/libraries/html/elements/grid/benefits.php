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
class JGridElementBenefits extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'benefits';

	public function fetchElement( $row, $node, $group )
	{
		$return = '';
		$key = trim( $node->attributes( 'key' ) );
		$translate = trim( $node->attributes( 't' ) );
		$Offices = $this->getBenefits();
		if ( !count( $Offices ) )
		{
			return 'Undefined';
		}

		$Value = C::_( $key, $row );
		if ( is_array( $Value ) )
		{
			$collect = [];
			foreach ( $Value as $id )
			{
				if ( $id )
				{
					$office = C::_( $id, $Offices );
					$Text = C::_( 'LIB_TITLE', $office );
					if ( $translate == 1 )
					{
						$Text = XTranslate::_( $Text );
					}
					$collect[] = $Text;
				}
			}
			$return = implode( ', ', $collect );
		}
		else
		{
			$office = C::_( $Value, $Offices );
			$Text = C::_( 'LIB_TITLE', $office );
			if ( $translate == 1 )
			{
				$Text = XTranslate::_( $Text );
			}
			$return = $Text;
		}
		return $return;

	}

	public function getBenefits()
	{
		static $Data = null;
		if ( is_null( $Data ) )
		{
			$query = 'select '
							. ' t.id, '
							. ' t.lib_title, '
							. ' t.lib_desc '
							. ' from lib_f_benefits t '
							. ' where t.id > 0 '
							. ' order by t.lib_title ';
			$Data = DB::LoadObjectList( $query, 'ID' );
		}
		return $Data;

	}

}
