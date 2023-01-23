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
class FilterElementGroup extends FilterElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'Group';

	public function fetchElement( $name, $id, $node, $config )
	{
		$class = '';
		$data = Helper::getWorkerGroups();
		$options = array();
		$options[] = HTML::_( 'select.option', -1, Text::_( 'Select Group' ) );
		foreach ( $data as $Item )
		{
			$options[] = HTML::_( 'select.option', C::_( 'ID', $Item ), C::_( 'LIB_TITLE', $Item ) );
		}
		$value = $this->GetConfigValue( $config['data'], $name );
		return HTML::_( 'select.genericlist', $options, $name, 'class="form-control' . $class . '" ', 'value', 'text', $value, $id );

	}

}
