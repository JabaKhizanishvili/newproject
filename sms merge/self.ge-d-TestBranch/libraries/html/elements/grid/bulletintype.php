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
class JGridElementBulletinType extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'bulletintype';

	public function fetchElement( $row, $node, $config )
	{
		$key = trim( $node->attributes( 'state_key' ) );
		$html = '';
		switch ( $row->{$key} )
		{
			case 1:
				$html = '<div class="yellow">' . Text::_( 'BL_Current' ) . '</div>';
				break;
			case 2:
				$html = '<div class="red">' . Text::_( 'BL_Close' ) . '</div>';
				break;
			case 3:
				$html = '<div class="green">' . Text::_( 'BL_Confirmed' ) . '</div>';
				break;
			default:
				$html = '<div class="red">' . Text::_( 'BL_Unknown' ) . '</div>';
				break;
		}
		return $html;

	}

}
