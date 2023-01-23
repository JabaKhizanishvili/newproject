<?php
require_once PATH_BASE . DS . 'libraries' . DS . 'Units.php';

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
class JElementUnit extends JElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'Unit';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$ORG = $this->_parent->get( 'ORG' );
		$parent = $node->attributes( 'parent' );
		if ( empty( $ORG ) && !empty( $parent ) )
		{
			$ORG = (int) Request::getState( $parent . '.display', 'org', '' );
		}

		$Depts = Units::getUnitList( $ORG );
		$options = array();
		$options[] = HTML::_( 'select.option', 0, Text::_( 'Select Org Place' ) );
		foreach ( $Depts as $dept )
		{
			$val = $dept->ID;
			$text = XTranslate::_( $dept->TITLE );
			$ULevel = $dept->ULEVEL;
			$options[] = HTML::_( 'select.option', $val, str_repeat( '- ', $ULevel ) . $text );
		}
		return HTML::_( 'select.genericlist', $options, '' . $control_name . '[' . $name . ']', ' class="form-control search-select" ', 'value', 'text', $value, $control_name . $name );

	}

}
