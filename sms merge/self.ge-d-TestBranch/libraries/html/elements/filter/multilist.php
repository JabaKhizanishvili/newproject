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
class FilterElementMultilist extends FilterElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'multilist';

	public function fetchElement( $name, $id, $node, $config )
	{
		$value = $this->GetConfigValue( $config['data'], $name );
		foreach ( $node->children() as $option )
		{
			$val = $option->attributes( 'value' );
			$text = $option->data();
			$options[] = HTML::_( 'select.option', $val, Text::_( $text ) );
		}

		if ( is_array( $options ) )
		{
			reset( $options );
		}
		$html = '<input type="hidden" name="' . $name . '[]" />';
		$html .= '<select name="' . $name . '[]" id="' . $id . '" ' . ' multiple ' . ' class="form-control kbd search-select">';
		$html .= HTMLSelect::Options( $options, 'value', 'text', $value, false );
		$html .= '</select>';
		return $html;

	}

}
