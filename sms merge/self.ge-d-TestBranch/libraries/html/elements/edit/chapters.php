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
class JElementChapters extends JElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'Chapters';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$Chapters = SalaryHelper::getChapterList();
		$Value = explode( ',', $value );
		$ID = $control_name . $name;
		foreach ( $Chapters as $Item )
		{
			$options[] = HTML::_( 'select.option', $Item->SID, $Item->LIB_TITLE );
		}
		if ( is_array( $options ) )
		{
			reset( $options );
		}
		$html = '<input type="hidden" name="' . $control_name . '[' . $name . ']' . '[]" />';
		$html .= '<select name="' . $control_name . '[' . $name . ']' . '[]" id="' . $ID . '" ' . ' multiple ' . ' class="form-control kbd search-select">';
		$html .= HTMLSelect::Options( $options, 'value', 'text', $Value, false );
		$html .= '</select>';
		return $html;
//		return HTML::_( 'select.genericlist', $options, '' . $name, ' multiple="multiple" size="10" ', 'value', 'text', $value, $id );

	}

}
