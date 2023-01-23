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
class JElementMyOrgsApps extends JElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'MyOrgsApps';

	public function fetchElement( $name, $V, $node, $control_name )
	{
		$value = $V;

		$all = trim( $node->attributes( 'allorgs' ) );
		$class = trim( $node->attributes( 'class' ) );
		$translate = trim( $node->attributes( 't' ) );
		$D = trim( $node->attributes( 'dropdown' ) );
		$D_options = array();
		$D_html = '';
		$D_class = '';
		$D_value = [];
		$collect_V = [];
		$collect_D = [];
		if ( !empty( $D ) )
		{
			foreach ( $V as $key => $val )
			{
				if ( $VS = explode( ',', $val ) )
				{
					$collect_V[] = $VS[0];
					$collect_D[$VS[0]] = $VS[1];
				}
			}
			if ( count( $collect_D ) )
			{
				$value = $collect_V;
				$D_value = $collect_D;
			}
			foreach ( $node->children() as $option )
			{
				$val = $option->attributes( 'value' );
				$text = $option->data();
				$D_options[] = HTML::_( 'select.option', $val, Text::_( $text ) );
			}
			$D_class = ' inline_dropdown ';
			Helper::SetJS( '$(".inline_dropdown").children("label").next("br").remove();$(".inline_dropdown").children("label").css("margin-bottom","8px");' );
		}

		$Depts = XGraph::GetMyOrgs();
		$allOrgs = '';
		if ( !empty( $all ) )
		{
			$Depts = $this->allOrgs();
			$allOrgs = ' allOrgs ';
		}
		$options = array();
		$All = array();
		foreach ( $Depts as $dept )
		{
			$All[] = $dept->ID;
			$val = $dept->ID;
			$text = XTranslate::_( $dept->LIB_TITLE );
			if ( $translate == 1 )
			{
				$text = XTranslate::_( $text );
			}
			if ( !empty( $D ) )
			{
				$D_html = HTML::_( 'select.genericlist', $D_options, 'params[' . $D . '][' . $val . ']', '', 'value', 'text', C::_( $val, $D_value ), 'flow_' . $name . $val );
			}
			$options[] = HTML::_( 'select.option', $val, $text . ' ' . $D_html );
		}
		if ( empty( $value ) )
		{
			$value = $All;
		}
		$html = '<div class="groups_parent">'
						. '<a class="select_all" href="javascript:void(0);" onclick="SelectAllCheckbox($(this).parent());">' . Text::_( 'Select All' ) . ' </a> | '
						. '<a class="deselect_all" href="javascript:void(0);" onclick="DeSelectAllCheckbox($(this).parent());" >' . Text::_( 'DeSelect All' ) . ' </a>'
						. '<br /> '
						. '<br /> ';
		$html .= '<div class="' . $allOrgs . ' ' . $class . $D_class . ' radio">';
		$html .= HTML::_( 'select.checkbox', $options, '' . $control_name . '[' . $name . '][]', '', 'value', 'text', $value, $control_name . $name, false, true );
		$html .= '</div>';
		$html .= '</div>';
		return $html;

	}

	public function allOrgs()
	{
		$Q = 'select '
						. ' id, '
						. ' t.lib_title '
						. ' from lib_unitorgs t '
						. ' where t.active=1 '
						. ' order by t.ordering asc';

		return DB::LoadObjectList( $Q );

	}

}
