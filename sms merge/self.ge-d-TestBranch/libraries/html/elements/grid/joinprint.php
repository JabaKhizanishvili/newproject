<?php

/**
 * @version		$Id: joinprint.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Framework
 * @copyright	Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
// Check to ensure this file is within the rest of the framework
/**
 * Renders a joinprint element
 *
 * @package 	WSCMS.Framework
 * @subpackage		Parameter
 * @since		1.5
 */
class JGridElementJoinPrint extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'JoinPrint';

	public function fetchElement( $row, $node, $group )
	{
		$translate = (int) $node->attributes( 't' );
		$Tscope = trim( $node->attributes( 'tscope' ) );
		$patern = trim( $node->attributes( 'patern' ) );
		$ex = explode( ' ', $patern );
		$patern = [];
		foreach ( $ex as $key )
		{
			$Text = C::_( trim( $key ), $row, '' );
			if ( $translate == 1 )
			{
				$Text = XTranslate::_( $Text, $Tscope );
			}

			$patern[] = $Text;
//			$patern = preg_replace( '/\b' . mb_strtolower( $key ) . '\b/', $value, $patern );
		}
		return implode( ' ', $patern );

	}

}
