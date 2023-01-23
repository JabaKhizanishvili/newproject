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
class JElementGroupsRel extends JElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'GroupsRel';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$Current = array_flip( explode( ',', $value ) );
		$Groups = self::getAllGroupsItems();
		$html = '<div class="groups_parent">'
						. '<a class="select_all" href="javascript:void(0);" onclick="SelectAllCheckbox($(\'.select_all\').parent());">' . Text::_( 'Select All' ) . ' </a> | '
						. '<a class="deselect_all" href="javascript:void(0);" onclick="DeSelectAllCheckbox($(this).parent());" >' . Text::_( 'DeSelect All' ) . ' </a>'
						. '<br /> '
						. '<br /> ';
		foreach ( $Groups as $R )
		{

			$chk = '';
			if ( isset( $Current[$R->ID] ) )
			{
				$chk = ' checked="checked" ';
			}
			if ( empty( $value ) )
			{
				$chk = ' checked="checked" ';
			}
			$Desc = C::_( 'LIB_DESC', $R, '' );
			if ( $Desc )
			{
				$Desc = ' ( ' . $Desc . ' ) ';
			}
			$html .= '<div class="level_0 org_groups_l" data-rel="orgl-' . $R->ORG . '">'
							. '<input type="checkbox" ' . $chk . ' name="' . $control_name . '[' . $name . '][]' . '" '
							. ' id="' . $control_name . $name . '_' . $R->ID . '" value="' . $R->ID . '"' . ' class="org_groups" data-rel="org-' . $R->ORG . '" '
							. '/>'
							. '<label for = "' . $control_name . $name . '_' . $R->ID . '">&nbsp;
			&nbsp;
			&nbsp;
			' . XTranslate::_( $R->LIB_TITLE ) . ' ' . $Desc . '</label>'
							. '</div>'
							. '<div class = "cls"></div>';
		}
		$html .= '</div>';
		$JS = 'SetWGroups();'
						. ' $("#paramsORG").change(function () { SetWGroups(); });'
		;
		Helper::SetJS( $JS );
		return $html;

	}

	public static function getAllGroupsItems()
	{
		$UserID = Users::GetUserID();
		$DirectTree = Helper::CheckTaskPermision( 'direct_subordianate_tree', 's' );
		$AdditionalTree = Helper::CheckTaskPermision( 'additional_subordianate_tree', 's' );
		$DirectTreeUnion = '';
		$AdditionalTreeUnion = '';
		if ( $DirectTree )
		{
			$DirectTreeUnion = ' or ww.person in (' . XStaffSchedule::GetChiefSubordinationsTree() . ') ';
		}
		if ( $AdditionalTree )
		{
			$AdditionalTreeUnion = ' or ww.person in (' . XStaffSchedule::GetChiefSubordinationsTree( 1 ) . ') ';
		}

		$query = 'select '
						. ' wg.id, '
						. ' wg.lib_title,'
						. ' wg.org, '
						. ' wg.lib_desc '
						. ' from lib_workers_groups wg '
						. ' where '
						. ' wg.id in('
						. ' select '
						. ' distinct (wg.group_id) '
						. ' from rel_workers_groups wg '
						. ' left join slf_worker ww on ww.id = wg.worker '
						. ' where '
						. ' wg.worker in ('
						. ' select '
						. ' wc.worker '
						. ' from rel_worker_chief wc '
						. ' where '
						. ' wc.chief_pid   =  ' . $UserID
						. ') ' . $DirectTreeUnion . $AdditionalTreeUnion . ')'
						. '   and wg.active = 0 '
		;
		$data = DB::LoadObjectList( $query );
		return $data;

	}

}
