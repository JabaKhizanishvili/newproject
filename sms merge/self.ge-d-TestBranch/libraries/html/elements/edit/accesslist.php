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
class JElementAccesslist extends JElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'accesslist';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$Graphs = $this->getStandartGraphs();
		$options = array();
		$options[] = HTML::_( 'select.option', -1, Text::_( 'FULL_ACCESS' ) );
		$options[] = HTML::_( 'select.option', 0, Text::_( 'NO_ACCESS' ) );
		foreach ( $Graphs as $Graph )
		{
			$val = $Graph->ID;
			$text = XTranslate::_( $Graph->LIB_TITLE );
			$options[] = HTML::_( 'select.option', $val, $text );
		}
		Helper::SetJS( 'setGraphActior(\'' . $control_name . $name . '\');' );

		return HTML::_( 'select.genericlist', $options, '' . $control_name . '[' . $name . ']', ' class="selectpicker form-control"  onchange="setGraphActior(\'' . $control_name . $name . '\');" ', 'value', 'text', $value, $control_name . $name );

	}

	public function getStandartGraphs()
	{
		$Query = 'select w.id, w.lib_title
  from lib_access_manager w
 where w.id > 0
   and w.active = 1
 order by w.lib_title';
		return DB::LoadObjectList( $Query );

	}

}
