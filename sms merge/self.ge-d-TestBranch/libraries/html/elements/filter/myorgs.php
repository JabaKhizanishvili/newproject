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
class FilterElementMyOrgs extends FilterElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'myorgs';

	public function fetchElement( $name, $id, $node, $config )
	{
//        $Data = Units::getWorkerOrgList();
//        $Data = [];
//        if (Helper::CheckTaskPermision( 'admin', C::_('_option', $config, false)))  {
		$Data = Units::getOrgList();
//        } else {
//            $Data = Units::getWorkerOrgList();
//        }

		$value = $this->GetConfigValue( $config['data'], $name );
		$options[] = HTML::_( 'select.option', 0, Text::_( 'ORG FILTER' ) );
		foreach ( $Data as $Item )
		{
			$val = $Item->ID;
			$text = XTranslate::_( $Item->TITLE );
			$options[] = HTML::_( 'select.option', $val, $text );
		}

		return HTML::_( 'select.genericlist', $options, $name, ' class="form-control" onchange="setFilter();" ', 'value', 'text', $value, $id );

	}

//	public function getOrgList()
//	{
//		$User = users::getUserID();
//		$Query = ' select '
//						. ' u.id, '
//						. ' u.lib_title title'
//						. ' from '
//						. ' lib_unitorgs u '
//						. ' where '
//						. ' u.id in ('
//						. ' select re.org '
//						. ' from hrs_workers re '
//						. ' left join rel_workers_groups r on r.org = re.org '
//						. ' left join lib_workers_groups l on l.id = r.GROUP_ID '
//						. ' where '
//						. ' u.active= 1'
//						. ' and '
//						. ' l.workers is not null '
//						. ' and re.parent_id = ' . DB::Quote( $User )
//						. ' group by re.org ' . ')'
//		;
//		return DB::LoadObjectList( $Query );
//	}
}
