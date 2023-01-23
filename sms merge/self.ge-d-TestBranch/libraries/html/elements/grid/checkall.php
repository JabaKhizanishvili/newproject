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
class JGridElementCheckAll extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'CheckAll';

	public function fetchElement( $row, $node, $group )
	{
		$Key = trim( $node->attributes( 'key' ) );
		if ( empty( $Key ) )
		{
			$Key = 'ID';
		}
		$class = $node->attributes( 'class' ) ? ' class="' . $node->attributes( 'class' ) . ' self-color self-border checknid" ' : ' class="self-color self-border checknid" ';
		return '<div class="radio"><input type="checkbox" value="' . $row->{$Key} . '" id="checknid' . $row->{$Key} . '" name="nid[]" ' . $class . '> <label for="checknid' . $row->{$Key} . '"></label></div>';

	}

}
