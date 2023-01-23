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
class JGridElementAdvancePrint extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'AdvancePrint';

	public function fetchElement( $row, $node, $config )
	{
		$html = '';
		$editView = $this->GetConfigValue( $config, '_option_edit', 'default' );
		$key = trim( $node->attributes( 'key' ) );
		$Task = C::_( 'task', $config, 'edit' );
		if ( $key )
		{
			if ( isset( $row->$key ) )
			{
				$html .= ' <a href="?option=advance_view&amp;tmpl=modal&amp;nid[]=' . $row->ID . '&amp;iframe=true&amp;height=97%&amp;width=97%" class="modaliframe">';
				$html .= $row->$key;
				$html .= ' </a>';
			}
		}
		return $html;

	}

}
