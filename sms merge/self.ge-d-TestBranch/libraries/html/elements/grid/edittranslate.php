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
class JGridElementEdittranslate extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'edittranslate';

	public function fetchElement( $row, $node, $config )
	{
		$html = '';
		$editView = $this->GetConfigValue( $config, '_option_edit', 'default' );
		$key = trim( $node->attributes( 'key' ) );
		$val = trim( $node->attributes( 'val' ) );
		$translate = trim( $node->attributes( 't' ) );
		if ( $key )
		{
			$label = $row->{$key};
			if ( $translate == 1 )
			{
				$label = XTranslate::_( $label );
			}
			$lng = C::_( 'To', $row );
			if ( isset( $row->{$key} ) )
			{
				$html .= ' <a href="?option=' . $editView . '&amp;task=new&lng=' . $lng . '&amp;hash=' . $row->{$val} . '" >';
				$html .= $label;
				$html .= ' </a>';
			}
		}
		return $html;

	}

}
