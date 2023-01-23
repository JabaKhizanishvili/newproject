<?php
/**
 * @version		$Id: ExcludeStaff.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Framework
 * @copyright	Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
// Check to ensure this file is within the rest of the framework

/**
 * Renders a ExcludeStaff element
 *
 * @package 	WSCMS.Framework
 * @subpackage		Parameter
 * @since		1.5
 */
class JElementExcludeStaff extends JElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'ExcludeStaff';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$options = array();
		if ( Users::GetUserData( 'USER_TYPE' ) == 2 )
		{
			$UserID = Users::GetUserID();
			$Data = $this->getChiefStaffStruct( $UserID );
			$Values = $this->getChiefStaffStructValues( $UserID );
			foreach ( $Data as $option )
			{
				$val = $option->IDX;
				$text = $option->DEP . ' - ' . $option->CHAP;
				$options[] = HTML::_( 'select.option', $val, $text );
			}
			return HTML::_( 'select.genericlist', $options, '' . $control_name . '[' . $name . '][]', ' multiple class="search-select"', 'value', 'text', $Values, $control_name . $name );
		}
		else
		{
			return '<div class="form-control-static">'
							. Text::_( 'Option is not avialable!' )
							. '</div>'
			;
		}

	}

	public function getChiefStaffStruct( $UserID )
	{
		$Query = 'select '
						. ' max(d.lib_title) dep, '
						. ' max(c.lib_title) chap, '
						. ' d.sid ||\'|\'|| c.sid idx '
						. ' from slf_persons w '
						. ' right join (select wc.worker from REL_WORKER_CHIEF wc where chief = ' . $UserID . ') wcw on wcw.worker = w.id '
						. ' left join lib_department d on d.sid = w.department '
						. ' left join lib_chapter c on c.sid = w.chapter '
						. ' group by d.sid ||\'|\'|| c.sid '
						. ' order by dep, chap '
		;
		return DB::LoadObjectList( $Query );

	}

	public function getChiefStaffStructValues( $UserID )
	{
		$Query = 'select '
						. ' t.department || \'|\' || t.chapter idx '
						. ' from REL_CHIEF_EXLUDE t '
						. ' where t.worker = ' . (int) $UserID
		;
		return DB::LoadList( $Query );

	}

}
