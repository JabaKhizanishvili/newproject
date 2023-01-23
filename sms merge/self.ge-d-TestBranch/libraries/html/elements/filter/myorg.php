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
class FilterElementMyOrg extends FilterElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'myorg';

	public function fetchElement( $name, $id, $node, $config )
	{

		$value = $this->GetConfigValue( $config['data'], $name );
		$Data = $this->getOrgList();
		$options[] = HTML::_( 'select.option', 0, Text::_( 'ORG FILTER' ) );
		foreach ( $Data as $Item )
		{
			$val = $Item->ID;
			$text = XTranslate::_( $Item->LIB_TITLE );
			$options[] = HTML::_( 'select.option', $val, $text );
		}

		return HTML::_( 'select.genericlist', $options, $name, ' class="form-control" onchange="setFilter();" ', 'value', 'text', $value, $id );

	}

	public function getOrgList()
	{
		$User = users::getUserID();
		$Query = ' select '
						. ' u.id, '
						. ' u.lib_title '
						. ' from '
						. ' hrs_workers re '
						. ' left join lib_unitorgs u on u.id = re.org '
						. ' where '
						. ' u.active= 1'
						. ' and re.parent_id = ' . DB::Quote( $User )
		;
		return DB::LoadObjectList( $Query );

	}

}
