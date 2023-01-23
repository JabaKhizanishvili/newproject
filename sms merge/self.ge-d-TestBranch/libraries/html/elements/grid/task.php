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
class JGridElementTask extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'Task';

	public function fetchElement( $row, $node, $config )
	{
		$Key = trim( $node->attributes( 'key' ) );
		$ShowKey = trim( $node->attributes( 'show_key' ) );
		$ID = C::_( $Key, $row );
		$ShowVal = C::_( $ShowKey, $row );
		foreach ( $node->children() as $option )
		{
			$Val = $option->attributes( 'value' );
			$Shows = array_flip( explode( ',', $option->attributes( 'show' ) ) );
			if ( !isset( $Shows[$ShowVal] ) )
			{
				continue;
			}
			Helper::getRowToolbar( $ID, $option->data(), C::_( '_option_edit', $config ), $Val, 1 );
			Text::_( $option->data() );
		}
		return '';

	}

}
