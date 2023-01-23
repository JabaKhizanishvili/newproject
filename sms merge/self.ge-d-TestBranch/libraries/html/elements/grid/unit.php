<?php
require_once PATH_BASE . DS . 'libraries' . DS . 'Units.php';

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
class JGridElementUnit extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'Unit';

	public function fetchElement( $row, $node, $group )
	{
		$Depts = Units::getUnitList();
		$key = trim( $node->attributes( 'key' ) );
		$translate = trim( $node->attributes( 't' ) );
		$multy = trim( $node->attributes( 'multy' ) );
		$Text = '';
		if ( isset( $row->{$key} ) )
		{
			$Text = C::_( $row->{$key} . '.TITLE', $Depts );
		}
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
					$Text = C::_( C::_( C::_( '1', $Parts ), $Item ) . '.TITLE', $Depts );
					if ( $translate == 1 )
					{
						$Text = XTranslate::_( $Text );
					}
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
		else
		{
			if ( $translate == 1 )
			{
				$Text = XTranslate::_( $Text );
			}
		}
		return $Text;

	}

}
