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
class JElementBenefittypes extends JElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'benefittypes';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		if ( PAYROLL != 1 )
		{
			return false;
		}

		$regularity = $node->attributes( 'benefit_regularity' );
		$List = $this->getBenefitTypes( $regularity );
		if ( !is_array( $value ) && !empty( $value ) )
		{
			$value = explode( '|', $value );
		}

		$html = '<div class="groups_parent">'
						. '<a class="select_all" href="javascript:void(0);" onclick="SelectAllCheckbox($(\'.select_all\').parent());">' . Text::_( 'Select All' ) . ' </a> | '
						. '<a class="deselect_all" href="javascript:void(0);" onclick="DeSelectAllCheckbox($(this).parent());" >' . Text::_( 'DeSelect All' ) . ' </a>'
						. '<br /> '
						. '<br /> ';
		$html .= '<div class="radio">';

		$br = false;
		foreach ( $List as $key => $cat )
		{
			$options = array();
			$cat_name = '';
			foreach ( $cat as $Item )
			{
				$val = $Item->ID;
				$text = XTranslate::_( $Item->LIB_TITLE );
				$cat_name = empty( $cat_name ) ? XTranslate::_( $Item->BENEFIT_CATEGORY ) : $cat_name;
				$options[] = HTML::_( 'select.option', $val, $text );
			}
			$html .= $br ? '<br>' : '';
			$html .= '<span style="margin-left:-10px;">' . $cat_name . '</span><br>';
			$html .= HTML::_( 'select.checkbox', $options, '' . $control_name . '[' . $name . '][]', '', 'value', 'text', $value, $control_name . $name, false, true );
			$br = true;
		}

		$html .= '</div>';
		$html .= '</div>';
		return $html;

	}

	public function getBenefitTypes( $regularity = -1 )
	{
		$Query = 'select '
						. ' p.id, '
						. ' p.lib_title, '
						. ' t.id ben_id, '
						. ' t.lib_title benefit_category '
						. ' from LIB_F_BENEFIT_TYPES p '
						. ' left join lib_f_benefits t on t.id = p.benefit '
						. ' where '
						. ' p.active > 0 '
						. ($regularity > 0 ? ' and p.regularity in (3, ' . $regularity . ')' : '')
						. ' order by p.lib_title asc';
		$result = DB::LoadObjectList( $Query );

		$collect = [];
		foreach ( $result as $key => $val )
		{
			$collect[$val->BEN_ID][] = $val;
		}
		return $collect;

	}

}
