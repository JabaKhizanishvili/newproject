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
class JGridElementMainunit extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'Mainunit';

	public function fetchElement( $row, $node, $group )
	{
		/* @var $node SimpleXMLElements */
		$key = trim( $node->attributes( 'key' ) );
		$LimitType = trim( $node->attributes( 'limit_type' ) );
		$Length = intval( $node->attributes( 'length' ) );
		$format = (int) $node->attributes( 'format' );
		$multy = trim( $node->attributes( 'multy' ) );
		$translate = trim( $node->attributes( 't' ) );
		$Tscope = trim( $node->attributes( 'tscope' ) );
		$MainUnits = Units::GetMainUnits();
		$Text = '';
		if ( isset( $row->{$key} ) )
		{
			$Text = trim( stripslashes( C::_( $row->{$key} . '.TITLE', $MainUnits ) ) );
			if ( $translate == 1 )
			{
				$Text = XTranslate::_( $Text, $Tscope );
			}
			switch ( $LimitType )
			{
				case 1:
					$Text = Helper::MakeToolTip( $Text, $Length, 0 );
					break;
				case 2:
					$Text = Helper::MakeToolTip( $Text, $Length, 1 );
					break;
			}
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
					$Text = C::_( C::_( '1', $Parts ), $Item );
					if ( $translate == 1 )
					{
						$Text = XTranslate::_( $Text );
					}
					switch ( $LimitType )
					{
						case 1:
							$Text = Helper::MakeToolTip( $Text, $Length, 0 );
							break;
						case 2:
							$Text = Helper::MakeToolTip( $Text, $Length, 1 );
							break;
					}
					$TT[] = $Text;
					$HTML[] = '<div class="key_div">'
									. '<span class="key_val">'
									. $Text
									. '</span>'
									. '</div>'
					;
				}
			}
			$Text = $multy == 'e' ? implode( ', ', $TT ) : '<div class="key_row">' . implode( '', $HTML ) . '</div>';
		}
		return $format > 0 ? number_format( $Text, $format ) : $Text;

	}

}
