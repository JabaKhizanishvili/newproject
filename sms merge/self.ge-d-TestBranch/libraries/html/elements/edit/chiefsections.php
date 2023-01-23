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
class JElementChiefSections extends JElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'ChiefSections';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$SectionID = 'params' . $name;
		$List = $this->getSectionList();
		$options = array();
		foreach ( $List as $item )
		{
			$val = $item->ID;
			$text = $item->DEPT_TITLE . ' - ' . $item->TITLE;
			$options[] = HTML::_( 'select.option', $val, $text, 'value', 'text' );
		}
		return HTML::_( 'select.genericlist', $options, $control_name . '[' . $name . ']', '', 'value', 'text', $value, $SectionID );

	}

	public function getSectionList()
	{
		$chiefSections = Helper::getChiefGroups();

		$query = 'select '
						. ' t.id, '
						. ' t.lib_title title,'
						. 't.dept_id,'
						. ' d.lib_title dept_title '
						. ' from lib_sections t '
						. ' left join lib_departments d '
						. ' on d.id=t.dept_id '
						. ' where '
						. ' t.id in ('
						. implode( ',', $chiefSections )
						. ')'
						. ' and t.active=1 '
						. ' order by t.dept_id asc, title asc';
		return DB::LoadObjectList( $query, 'ID' );

	}

}
