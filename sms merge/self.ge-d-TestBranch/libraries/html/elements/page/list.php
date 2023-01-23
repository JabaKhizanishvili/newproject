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
class PageElementList extends PageElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'List';

	public function fetchElement( $row, $node, $group )
	{
		$Key = $node->attributes( 'key' );
		$Value = C::_( $Key, $row );
		static $options = array();
		if ( !isset( $options[$Key] ) )
		{
			$options[$Key] = array();
			foreach ( $node->children() as $option )
			{
				$val = $option->attributes( 'value' );
				$text = $option->data();
				$options[$Key][$val] = Text::_( $text );
			}
		}
		return C::_( $Key . '.' . $Value, $options );

	}

}
