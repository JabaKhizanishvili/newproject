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
class JGridElementSwitch extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'Switch';

	public function fetchElement( $row, $node, $config )
	{
		$multy = trim( $node->attributes( 'multy' ) );
		$key = trim( $node->attributes( 'key' ) );
		$Value = C::_( $key, $row );
		$Start = '';
		$End = '';
		$Text = '';
		$options = [];

		foreach ( $node->children() as $option )
		{
			$val = $option->attributes( 'value' );
			$Color = $option->attributes( 'color' );
			if ( $Color )
			{
				$Start = '<div class="' . $Color . ' text-bold">';
				$End = '</div>';
			}
			if ( $Value == $val )
			{
				$Text = $Start . Text::_( $option->data() ) . $End;
			}
			$options[$val] = Text::_( $option->data() );
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
					$Text = C::_( C::_( C::_( '1', $Parts ), $Item ), $options );
					$TT[] = $Text;
					$HTML[] = '<div class="key_div">'
									. '<span class="key_val">'
									. $Text
									. '</span>'
									. '</div>'
					;
				}
				$Text = $multy == 'e' ? implode( ", ", $TT ) : '<div class="key_row">' . implode( '', $HTML ) . '</div>';
			}
		}
		return $Text;

	}

}
