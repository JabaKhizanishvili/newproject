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
class JGridElementorgtip extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'Print';

	public function fetchElement( $row, $node, $group )
	{
		/* @var $node SimpleXMLElements */
		$key = trim( $node->attributes( 'key' ) );
		$TipKey = trim( $node->attributes( 'tipkey' ) );
		$Parts = explode( '.', $key );
		$TipParts = explode( '.', $TipKey );
		$Base = (array) C::_( C::_( '0', $Parts ), $row, array() );
		$Text = '';
		$HTML = array();
		if ( count( $Base ) )
		{
			foreach ( $Base as $Item )
			{
				$Text = C::_( C::_( '1', $Parts ), $Item );
				if ( empty( $Text ) )
				{
					$HTML[] = '<div class="key_div">'
									. '<span class="key_val">'
									. ' &nbsp;'
									. '</span>'
									. '</div>'
					;
				}
				else
				{
					$TipText = C::_( C::_( '1', $TipParts ), $Item );
					$HTML[] = Helper::MakeDoubleToolTip( $Text, $TipText );
				}
			}
		}
		return '<div class="key_row">' . implode( '', $HTML ) . '</div>';

	}

}
