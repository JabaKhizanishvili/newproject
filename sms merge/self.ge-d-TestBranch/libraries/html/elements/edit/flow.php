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
class JElementFlow extends JElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'flow';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$Data = TaskHelper::getFlows( 1 );
		$translate = trim( $node->attributes( 't' ) );
		$options = array();
		$options[] = HTML::_( 'select.option', 0, Text::_( 'Select Flow' ) );
		foreach ( $Data as $item )
		{
			$Text = $item->LIB_TITLE;
			if ( $translate == 1 )
			{
				$Text = XTranslate::_( $Text );
			}
			$options[] = HTML::_( 'select.option', $item->ID, $Text, 'value', 'text' );
		}
		return HTML::_( 'select.genericlist', $options, $control_name . '[' . $name . ']', ' class="form-control" ', 'value', 'text', $value, $control_name . $name );

	}

}
