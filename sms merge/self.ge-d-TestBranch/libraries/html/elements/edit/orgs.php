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
class JElementOrgs extends JElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'MyOrgs';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$Depts = XGraph::GetMyOrgs();
		$options = array();
//		$options[] = HTML::_( 'select.option', 0, Text::_( 'Select Org Place' ) );
		$All = array();
		foreach ( $Depts as $dept )
		{
			$All[] = $dept->ID;
			$val = $dept->ID;
			$text = $dept->LIB_TITLE;
			$options[] = HTML::_( 'select.option', $val, $text );
		}
		if ( empty( $value ) )
		{
			$value = $All;
		}
		$html = '<div class="groups_parent">'
						. '<a class="select_all" href="javascript:void(0);" onclick="SelectAllCheckbox($(\'.select_all\').parent());">' . Text::_( 'Select All' ) . ' </a> | '
						. '<a class="deselect_all" href="javascript:void(0);" onclick="DeSelectAllCheckbox($(this).parent());" >' . Text::_( 'DeSelect All' ) . ' </a>'
						. '<br /> '
						. '<br /> ';
		$html .= '<div class="radio">';
		$html .= HTML::_( 'select.checkbox', $options, '' . $control_name . '[' . $name . '][]', '', 'value', 'text', $value, $control_name . $name, false, true );
		$html .= '</div>';
		$html .= '</div>';
		return $html;

	}

}
