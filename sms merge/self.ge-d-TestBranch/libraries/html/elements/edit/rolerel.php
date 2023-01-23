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
class JElementRoleRel extends JElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'RoleRel';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$MenuID = Collection::get( '0', Request::getVar( 'nid' ), 0 );
		$Roles = self::getAllRoleItems( $MenuID );
		$html = '<div class="groups_parent">'
						. '<div class="cls">'
						. '<a class="select_all" href="javascript:void(0);" onclick="SelectAllCheckbox($(\'.select_all\').parent().parent());" style="float:none;">' . Text::_( 'Select All' ) . ' </a> | '
						. '<a class="deselect_all" href="javascript:void(0);" onclick="DeSelectAllCheckbox($(this).parent().parent());"  style="float:none;">' . Text::_( 'DeSelect All' ) . ' </a> '
						. '</div><div class="cls"></div>'
						. '<br /> ';
		foreach ( $Roles as $R )
		{
			$chk = '';
			if ( $R->MENU == $MenuID )
			{
				$chk = ' checked="checked" ';
			}
			$Desc = Collection::get( 'LIB_DESC', $R, '' );
			if ( $Desc )
			{
				$Desc = ' ( ' . $Desc . ' ) ';
			}
			$html .= '<div class="level_0 radio">'
							. '<input type="checkbox" ' . $chk . ' name="' . $control_name . '[' . $name . '][]' . '" '
							. ' class="self-border" id="' . $control_name . $name . '_' . $R->ID . '" value="' . $R->ID . '"'
							. '/>'
							. '<label for="' . $control_name . $name . '_' . $R->ID . '">' . XTranslate::_( $R->LIB_TITLE ) . $Desc . '</label>'
							. $this->getTasks( $name, $MenuID, $R, $control_name )
							. '</div>'
							. '<div class="cls"></div>';
		}
		$html .= '</div>';
		return $html;

	}

	public function getTasks( $name, $MenuID, $R, $control_name )
	{
		$Tasks = json_decode( Collection::get( 'PARAMS', $R, '[]' ) );
		$Menu = MenuConfig::getInstance();
		$menuItem = $Menu->getItem( 'ID', $MenuID );
		$XMLFile = PATH_BASE . DS . 'components' . DS . Collection::get( 'LIB_OPTION', $menuItem ) . DS . 'config.xml';
		$html = '';
		if ( is_file( $XMLFile ) )
		{
			$XMLDoc = Helper::loadXMLFile( $XMLFile );
			$Columns = $XMLDoc->getElementByPath( 'tasks' )->children();
			/* @var $Column SimpleXMLElements  */
			foreach ( $Columns as $Column )
			{
				$name = $Column->attributes( 'name' );
				$chk = '';
				if ( Collection::get( $name, $Tasks, null ) )
				{
					$chk = ' checked="checked" ';
				}
				$html .= '<div class="cls"></div>'
								. '<div class="role_tasks">'
								. '<input type="checkbox"  name="' . $control_name . '[' . $R->ID . '][' . $name . ']' . '" '
								. ' id="' . $control_name . $name . '_' . $R->ID . $name . '" value="1" '
								. $chk
								. '/>'
								. '<label for="' . $control_name . $name . '_' . $R->ID . $name . '"> - ' . Text::_( $name ) . '</label>'
								. '</div>'
				;
			}
		}
		return $html;

	}

	public static function getAllRoleItems( $menuID )
	{
		$query = ' select * from lib_roles r, '
						. ' rel_roles_menus rm '
						. ' where r.active >-1 '
						. ' and rm.role(+) = r.id '
						. ' and rm.menu(+) = ' . $menuID
						. ' order by r.ordering asc '
		;
		$data = DB::LoadObjectList( $query );
		return $data;

	}

}
