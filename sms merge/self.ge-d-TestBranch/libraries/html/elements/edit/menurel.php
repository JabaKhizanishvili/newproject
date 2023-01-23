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
class JElementMenuRel extends JElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'MenuRel';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$Menu = MenuConfig::getInstance();
		$Menus = $Menu->getAllMenuItems( false );
		$html = '<div class="checksBox">';
		$data = array_flip( explode( ',', $value ) );
		$K = '';
		$KClass = '';
		foreach ( $Menus as $M )
		{
			$M->LIB_TITLE = XTranslate::_( $M->LIB_TITLE );
			$chk = '';
			if ( isset( $data[$M->ID] ) )
			{
				$chk = ' checked="checked" ';
			}
			$Desc = Collection::get( 'LIB_DESC', $M, '' );
			if ( $Desc )
			{
				$Desc = ' ( ' . $Desc . ' ) ';
			}
			if ( $M->LIB_LEVEL == 0 && $K != $M->ID )
			{
				$KClass = ' itemrow_' . $M->ID;
			}
			$Rel = '';
			if ( $M->LIB_LEVEL == 0 )
			{
				$Rel = ' data-rel="' . $M->ID . '"';
			}
			$html .= '<div class="level_' . $M->LIB_LEVEL . $KClass . ' radio" ' . $Rel . ' >'
							. '<input type="checkbox" ' . $chk . ' class="self-border" name="' . $control_name . '[' . $name . '][]' . '" '
							. ' id="' . $control_name . $name . '_' . $M->ID . '" value="' . $M->ID . '"'
							. '/>'
							. '<label for="' . $control_name . $name . '_' . $M->ID . '">' . str_repeat( ' - ', $M->LIB_LEVEL ) . $M->LIB_TITLE . $Desc . '</label>'
							. $this->getTasks( $name, $value, $M, $control_name, $KClass )
							. '</div>'
							. '<div class="cls"></div>';
		}
		$html .= '</div>';
		return $html;

	}

	public function getTasks( $name, $value, $M, $control_name, $KClass )
	{
		static $Tasks = null;
		$RoleID = Collection::get( 0, Request::getVar( 'nid' ), false );
		if ( is_null( $Tasks ) && $RoleID )
		{
			$Tasks = Helper::getRolesConfig( $RoleID, 'MENU' );
		}
		$XMLFile = PATH_BASE . DS . 'components' . DS . Collection::get( 'LIB_OPTION', $M ) . DS . 'config.xml';
		$html = '';
		if ( is_file( $XMLFile ) )
		{
			$XMLDoc = Helper::loadXMLFile( $XMLFile );
			$Columns = $XMLDoc->getElementByPath( 'tasks' )->children();
			/* @var $Column SimpleXMLElements  */
			foreach ( $Columns as $Column )
			{
				$name = $Column->attributes( 'name' );
				$DefValue = $RoleID ? null : $Column->attributes( 'default' );
				$chk = '';
				if ( Collection::get( $M->ID . '.PARAMS.' . $name, $Tasks, $DefValue ) )
				{
					$chk = ' checked="checked" ';
				}
				$html .= '<div class="cls"></div>'
								. '<div class="role_tasks ' . $KClass . '">'
								. '<input type="checkbox"  name="' . $control_name . '[' . $M->ID . '][' . $name . ']' . '" '
								. ' id="' . $control_name . $name . '_' . $M->ID . $name . '" value="1" '
								. $chk
								. '/>'
								. '<label for="' . $control_name . $name . '_' . $M->ID . $name . '"> - ' . Text::_( $name ) . '</label>'
								. '</div>'
				;
			}
		}
		return $html;

	}

}
