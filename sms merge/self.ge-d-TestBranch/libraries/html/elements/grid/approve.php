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
class JGridElementApprove extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'Approve';

	public function fetchElement( $row, $node, $config )
	{
		$key = trim( $node->attributes( 'state_key' ) );
		if ( $row->{$key} > 0 )
		{
			$html = '<div class="green">' . Text::_( 'Approved' ) . '</div>';
		}
		else
		{
			$html = '<div class="red">' . Text::_( 'Not Approved' ) . '</div>';
		}
		return $html;

	}

}