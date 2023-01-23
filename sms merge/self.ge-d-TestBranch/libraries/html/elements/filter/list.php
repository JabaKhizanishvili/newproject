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
class FilterElementList extends FilterElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'List';

	public function fetchElement( $name, $id, $node, $config )
	{
		$Default = $node->attributes( 'default' );
		$value = $this->GetConfigValue( $config['data'], $name, $Default );
		$class = ( $node->attributes( 'class' ) ? ' class="' . $node->attributes( 'class' ) . '" ' : ' class="form-control" ' );
		$size = ( $node->attributes( 'size' ) ? ' size="' . $node->attributes( 'size' ) . '" ' : '' );
		$options = array();
		foreach ( $node->children() as $option )
		{
			$val = $option->attributes( 'value' );
			$text = $option->data();
			$options[] = HTML::_( 'select.option', $val, Text::_( $text ) );
		}

		return HTML::_( 'select.genericlist', $options, '' . $name, $class . $size, 'value', 'text', $value, $id );

	}

}
