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
class JElementVisitor extends JElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'Visitor';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$Depts = $this->getVisitorList();
		$options[] = HTML::_( 'select.option', 0, Text::_( 'Visitor FILTER' ) );
		foreach ( $Depts as $dept )
		{
			$val = $dept->ID;
			$text = $dept->TITLE;
			$options[] = HTML::_( 'select.option', $val, $text );
		}

		return HTML::_( 'select.genericlist', $options, '' . $control_name . '[' . $name . ']', ' class="form-control" ', 'value', 'text', $value, $control_name . $name );

	}

	public function getVisitorList()
	{
		$query = 'select '
						. ' id, '
						. ' t.lib_title title '
						. ' from lib_visitors t '
						. ' where t.active=1 '
						. ' order by title asc';
		return DB::LoadObjectList( $query );

	}

}
