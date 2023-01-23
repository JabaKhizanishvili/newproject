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
class JGridElementSalarytypes extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'salarytypes';

	public function fetchElement( $row, $node, $group )
	{
		$key = trim( $node->attributes( 'key' ) );
		$multy = trim( $node->attributes( 'multy' ) );
		$Staff_Schedules = $this->Staff_Schedule();
		$Value = C::_( $key, $row );
		$Text = C::_( $Value . '.TITLE', $Staff_Schedules );
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
					$Text = C::_( C::_( C::_( '1', $Parts ), $Item ) . '.TITLE', $Staff_Schedules );
					$TT[] = $Text;
					$HTML[] = '<div class="key_div">'
									. '<span class="key_val">'
									. $Text
									. '</span>'
									. '</div>'
					;
				}
				$Text = $multy == 'e' ? implode( ', ', $TT ) : '<div class="key_row">' . implode( '', $HTML ) . '</div>';
			}
		}
		return $Text;

	}

	public function Staff_Schedule()
	{
		static $Offices = null;
		if ( is_null( $Offices ) )
		{
			$query = 'select '
							. ' id, '
							. ' t.lib_title title '
							. ' from lib_f_salary_types  t ';
			$Offices = DB::LoadObjectList( $query, 'ID' );
		}
		return $Offices;

	}

}
