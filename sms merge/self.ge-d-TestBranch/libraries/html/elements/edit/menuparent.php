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
class JElementMenuParent extends JElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'MenuParent';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$Menu = MenuConfig::getInstance();
		$Menus = $Menu->getAllMenuItems( false );
		$options[] = HTML::_( 'select.option', 0, Text::_( 'First Level' ) );
		foreach ( $Menus as $M )
		{
			$val = $M->ID;
			$label = XTranslate::_( $M->LIB_TITLE );
			$text = str_repeat( ' - ', $M->LIB_LEVEL ) . $label;
			$options[] = HTML::_( 'select.option', $val, $text );
		}

		return HTML::_( 'select.genericlist', $options, '' . $control_name . '[' . $name . ']', ' class="form-control search-select" ', 'value', 'text', $value, $control_name . $name );

	}

}
