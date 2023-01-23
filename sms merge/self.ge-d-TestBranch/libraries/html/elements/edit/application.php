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
class JElementApplication extends JElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'Application';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$Apps = AppHelper::getApplicationList();
		$options[] = HTML::_( 'select.option', 0, Text::_( 'Application FILTER' ) );
		foreach ( $Apps as $dept )
		{
			$val = $dept->ID;
			$text = $dept->TITLE;
			$options[] = HTML::_( 'select.option', $val, $text );
		}

		return HTML::_( 'select.genericlist', $options, '' . $control_name . '[' . $name . ']', ' class="form-control" ', 'value', 'text', $value, $control_name . $name );

	}

}
