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
class JGridElementconfiglink extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'configlink';

	public function fetchElement( $row, $node, $config )
	{
		$html = '';
		$editView = C::_( 'OPTION', $row );

		$key = trim( $node->attributes( 'key' ) );
		$Task = Collection::get( 'task', $config, 'edit' );
		if ( $key )
		{
			if ( isset( $row->{$key} ) )
			{
				///?option=config&c=$row->{$key} &tmpl=modal&iframe=true&width=100%25&height=100%25
				$html .= ' <a href="?option=config&amp;c=' . $editView . '&amp;task=' . $Task . '" >';
				$html .= $row->{$key};
				$html .= ' </a>';
			}
		}
		return $html;

	}

}
