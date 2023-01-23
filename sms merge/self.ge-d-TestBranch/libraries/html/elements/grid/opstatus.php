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
class JGridElementOpstatus extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'opstatus';

	public function fetchElement( $row, $node, $config )
	{
		$key = trim( $node->attributes( 'key' ) );
		$html = '';
		switch ( $row->{$key} )
		{
			case 1:
				$html = '<div class="green">' . Text::_( 'done' ) . '</div>';
				break;
			case 0:
				$html = '<div class="yellow">' . Text::_( 'pending' ) . '</div>';
				break;
			default:
			case -2:
				$html = '<div class="red">' . Text::_( 'stopped' ) . '</div>';
				break;
			case -6:
				$html = '<div style="color:orange;">' . Text::_( 'rollbacked' ) . '</div>';
				break;
		}
		return $html;

	}

}
