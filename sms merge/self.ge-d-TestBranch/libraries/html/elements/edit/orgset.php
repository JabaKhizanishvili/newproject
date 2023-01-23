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
class JElementOrgset extends JElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'orgset';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$task = $node->attributes( 'task' );
		if ( empty( $task ) )
		{
			$task = 'generate';
		}
		$Depts = [];
		$option = Request::getCmd( 'option' );
		$action = 'doAction(\'' . $option . '\',\'' . $task . '\', 0, 0);';
		if ( $node->attributes( 'limitorg' ) == 1 )
		{
			$Depts = (array) XGraph::GetMyOrgs();
		}
		else
		{
			$Depts = (array) Units::getOrgList();
		}
		if ( count( $Depts ) == 1 && !isset( $value ) )
		{
			Helper::SetJS( $action );
		}

		$options = array();
		$options[] = HTML::_( 'select.option', 0, Text::_( 'Select Org Place' ) );
		foreach ( $Depts as $dept )
		{
			$val = $dept->ID;
			$text = XTranslate::_( C::getVarIf( 'TITLE', $dept, null, 'LIB_TITLE' ) );
			$options[] = HTML::_( 'select.option', $val, $text );
		}
		return HTML::_( 'select.genericlist', $options, '' . $control_name . '[' . $name . ']', ' class="form-control" onchange="' . $action . '"', 'value', 'text', $value, $control_name . $name );

	}

}
