<?php
/**
 * @version		$Id: sql.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Framework
 * @copyright	Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
// Check to ensure this file is within the rest of the framework

/**
 * Renders a SQL element
 *
 * @package 	WSCMS.Framework
 * @subpackage		Parameter
 * @since		1.5
 */
class FilterElementHRGroupsRegion extends FilterElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'HRGroupsRegion';

	public function fetchElement( $name, $id, $node, $config )
	{
		$key = 'ID';
		$val = 'TITLE';
		$class = '';
		$data = Helper::getAllWorkerGroupsRegion();
		if ( !count( $data ) )
		{
			$select_label = 'There No Groups';
			$data = array(
					array(
							$key => '0',
							$val => Text::_( $select_label )
					)
			);
		}
		$value = $this->GetConfigValue( $config['data'], $name );
//		$js = '$(\'#' . $id . '\').chosen();';
//		Helper::SetJS( $js );
		return HTML::_( 'select.genericlist', $data, $name, 'class="filter_droplist form-control search-select' . $class . '" onchange="setFilter();" ', $key, $val, $value, $id );

	}

}
