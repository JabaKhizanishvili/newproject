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
class JGridElementEditApplication extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'EditApplication';

	public function fetchElement( $row, $node, $config )
	{
		$html = '';
		$Apps = AppHelper::getApplicationList();
		$editView = $this->GetConfigValue( $config, '_option_edit', 'default' );
		$key = trim( $node->attributes( 'key' ) );
		$ID = C::_( $key, $row );
		$Task = C::_( 'task', $config, 'edit' );
		if ( $key )
		{
			$html .= ' <a href="?option=' . $editView . '&amp;task=' . $Task . '&amp;nid[]=' . $row->ID . '" >';
			$html .= C::_( $ID . '.TITLE', $Apps );
			$html .= ' </a>';
		}
		return $html;

	}

}
