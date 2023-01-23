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
class JGridElementLink extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'Link';

	public function fetchElement( $row, $node, $config )
	{
		$html = '';
		$editView = $this->GetConfigValue( $config, '_option_edit', 'default' );
		$key = trim( $node->attributes( 'key' ) );
		if ( $key )
		{
			if ( isset( $row->{$key} ) )
			{
				$html .= ' <a href="' . $row->{$key} . '" target="_blank" >';
				$html .= HTML::image( 'templates/images/world_link.png', '' );
				$html .= ' </a>';
			}
		}
		return $html;

	}

}
