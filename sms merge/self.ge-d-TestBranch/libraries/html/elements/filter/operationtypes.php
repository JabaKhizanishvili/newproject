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
class FilterElementOperationtypes extends FilterElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'operationtypes';

	public function fetchElement( $name, $id, $node, $config )
	{
		$class = ( $node->attributes( 'class' ) ? ' class="' . $node->attributes( 'class' ) . ' form-control"  ' : ' class="form-control" ' );
		$value = $this->GetConfigValue( $config['data'], $name );
		$Change = ' onchange="setFilter();" ';
		$options = array();
		$options[] = HTML::_( 'select.option', -1, Text::_( 'Select category' ) );
		$options[] = HTML::_( 'select.option', 1, Text::_( 'assignment' ) );
		$options[] = HTML::_( 'select.option', 2, Text::_( 'changing' ) );
		$options[] = HTML::_( 'select.option', 3, Text::_( 'release' ) );
		if ( PAYROLL )
		{
			$options[] = HTML::_( 'select.option', 4, Text::_( 'benefits' ) );
		}
		$options[] = HTML::_( 'select.option', 5, Text::_( 'new_schedulechanging' ) );
		$options[] = HTML::_( 'select.option', 6, Text::_( 'rollback' ) );
		$options[] = HTML::_( 'select.option', 7, Text::_( 'schedulechanging' ) );

		return HTML::_( 'select.genericlist', $options, $name, $class . $Change, 'value', 'text', $value, $id );

	}

}
