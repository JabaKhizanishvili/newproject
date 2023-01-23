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
class JGridElementNumberdiff extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'numberdiff';

	public function fetchElement( $row, $node, $group )
	{
		/* @var $node SimpleXMLElements */
		$polus = trim( $node->attributes( 'polus' ) );
		$color = trim( $node->attributes( 'color', 'red' ) );

		$key1 = trim( $node->attributes( 'n1' ) );
		$N1 = (int) C::_( $key1, $row, 0 );
		$key2 = trim( $node->attributes( 'n2' ) );
		$N2 = (int) C::_( $key2, $row, 0 );

		$R = $N1 - $N2;
		if ( $R <= 0 && $polus )
		{
			return ' ';
		}

		return '<strong style="color:' . $color . ';">' . $R . '</strong>';

	}

}
