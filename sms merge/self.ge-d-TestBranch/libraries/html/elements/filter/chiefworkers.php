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
class FilterElementChiefWorkers extends FilterElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'ChiefWorkers';

	public function fetchElement( $name, $id, $node, $config )
	{
		$value = $this->GetConfigValue( $config['data'], $name );
		$Depts = $this->getLibList();
		$options[] = HTML::_( 'select.option', 0, Text::_( 'Workers FILTER' ) );
		foreach ( $Depts as $dept )
		{
			$val = $dept->ID;
			$text = $dept->TITLE;
			$options[] = HTML::_( 'select.option', $val, $text );
		}
		$html = HTML::_( 'select.genericlist', $options, $name, ' class="form-control search-select" onchange="setFilter();" ', 'value', 'text', $value, $id );
//        $js = '$(\'#' . $control_name . $name . '\').chosen();';
//        Helper::SetJS($js);
//		$html .= '</div>';
		return $html;

	}

	protected function getLibList()
	{
		$query = 'select '
						. ' t.id, '
						. ' t.firstname || \' \' || t.lastname || \' - \' || t.org_name title '
						. ' from hrs_workers t '
						. ' where '
						. ' t.active = 1 '
						. ' and t.id in (select wc.worker from rel_worker_chief wc where wc.chief in (select m.id from hrs_workers m where m.PARENT_ID =  ' . Users::GetUserID() . ' )) '
						. ' order by title asc';
		return DB::LoadObjectList( $query );

	}

	protected function option( $value, $text = '', $additional = false )
	{
		$obj = new stdClass;
		$obj->value = $value;
		$obj->text = trim( $text ) ? $text : $value;
		$obj->add = $additional;
		return $obj;

	}

}
