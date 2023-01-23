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
class JElementChiefWorkers extends JElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'ChiefWorkers';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$org = $node->attributes( 'limitorg' );
		$ORG = '';
		if ( $org == 1 )
		{
			$ORG = ' and t.org = ' . C::_( '_registry._default.data.ORG', $this->_parent );
		}

		$Depts = $this->getLibList( $ORG );

		$options[] = HTML::_( 'select.option', 0, Text::_( 'Workers FILTER' ) );
		foreach ( $Depts as $dept )
		{
			$val = $dept->ID;
			$text = $dept->TITLE;
			$options[] = HTML::_( 'select.option', $val, $text );
		}
		$html = HTML::_( 'select.genericlist', $options, $control_name . '[' . $name . ']', ' class="form-control search-select" ', 'value', 'text', $value, $control_name . $name );
		return $html;

	}

	protected function getLibList( $ORG )
	{
		$DirectTree = Helper::CheckTaskPermision( 'direct_subordianate_tree', 's' );
		$AdditionalTree = Helper::CheckTaskPermision( 'additional_subordianate_tree', 's' );
		$DirectTreeUnion = '';
		$AdditionalTreeUnion = '';
		if ( $DirectTree )
		{
			$DirectTreeUnion = ' or t.parent_id  in (' . XStaffSchedule::GetChiefSubordinationsTree() . ') ';
		}
		if ( $AdditionalTree )
		{
			$AdditionalTreeUnion = ' or t.parent_id  in (' . XStaffSchedule::GetChiefSubordinationsTree( 1 ) . ') ';
		}

		$query = 'select '
						. ' t.id, '
						. ' t.firstname || \' \' || t.lastname || \' - \' || t.org_name title '
						. ' from hrs_workers t '
						. ' where '
						. ' t.active = 1 '
						. ' and t.id in (select wc.worker_opid from rel_worker_chief wc where wc.chief_pid in ( ' . Users::GetUserID() . ' )  and wc.clevel in (0, 1)) ' . $DirectTreeUnion . $AdditionalTreeUnion
						. $ORG
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
