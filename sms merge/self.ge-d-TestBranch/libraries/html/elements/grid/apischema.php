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
class JGridElementApiSchema extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'ApiSchema';

	public function fetchElement( $row, $node, $group )
	{
		/* @var $node SimpleXMLElements */
		$key = trim( $node->attributes( 'key' ) );
		if ( isset( $row->{$key} ) )
		{
			$Uri = URI::getInstance()->toString( array( 'scheme', 'user', 'pass', 'host', 'port' ) ) . '/api/OAuth/Schema/' . $row->{$key};
			$Text = '<a target="_blank" href="' . $Uri . '">' . Text::_( 'View' ) . '</a>';
		}
		return $Text;

	}

}
