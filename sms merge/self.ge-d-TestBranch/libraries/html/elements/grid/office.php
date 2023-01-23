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
class JGridElementOffice extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'Office';

	public function fetchElement( $row, $node, $group )
	{
		$key = trim( $node->attributes( 'key' ) );
		$Offices = $this->getOfficeList();
		$translate = trim( $node->attributes( 't' ) );
		$Value = C::_( $key, $row );
		$multy = trim( $node->attributes( 'multy' ) );
		if ( $multy == 1 || $multy == 'e' )
		{
			$Parts = explode( '.', $key );
			$Base = (array) C::_( C::_( '0', $Parts ), $row, array() );
			$HTML = array();
			$TT = array();
			if ( count( $Base ) )
			{
				foreach ( $Base as $Item )
				{
					$result = [];
					$offices = explode( ',', C::_( C::_( '1', $Parts ), $Item ) );
					foreach ( $offices as $val )
					{
						if ( $val )
						{
							$Text = C::_( $val . '.TITLE', $Offices );
							if ( $translate == 1 )
							{
								$Text = XTranslate::_( $Text );
							}
							$result[] = $Text;
						}
					}
					$TT[] = implode( ', ', $result );
					$HTML[] = '<div class="key_div">'
									. '<span class="key_val">'
									. implode( ', ', $result )
									. '</span>'
									. '</div>'
					;
				}
				return $multy == 'e' ? implode( ', ', $TT ) : '<div class="key_row">' . implode( '', $HTML ) . '</div>';
			}
		}
		$Text = C::_( $Value . '.TITLE', $Offices );
		if ( $translate == 1 )
		{
			$Text = XTranslate::_( $Text );
		}
		return $Text;

	}

	public function getOfficeList()
	{
		static $Offices = null;
		if ( is_null( $Offices ) )
		{
			$query = 'select '
							. ' id, '
							. ' t.lib_title title '
							. ' from lib_offices t ';
			$Offices = DB::LoadObjectList( $query, 'ID' );
		}
		return $Offices;

	}

}
