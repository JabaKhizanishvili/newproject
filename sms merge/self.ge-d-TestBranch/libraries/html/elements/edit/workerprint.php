<?php
/**
 * @version		$Id: Print.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Framework
 * @copyright	Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
// Check to ensure this file is within the rest of the framework

/**
 * Renders a Print element
 *
 * @package 	WSCMS.Framework
 * @subpackage		Parameter
 * @since		1.5
 */
class JElementWorkerprint extends JElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'workerprint';

	public function fetchElement( $name, $valueIN, $node, $control_name )
	{
		$Type = trim( $node->attributes( 'wtype' ) );
		if ( $Type == 'worker' )
		{
			$w_data = XGraph::getWorkerDataSch( $valueIN );
		}
		else
		{
			$w_data = XGraph::GetOrgUser( $valueIN );
		}

		$worker_name = C::_( 'FIRSTNAME', $w_data ) . ' ' . C::_( 'LASTNAME', $w_data );
		return '<div class="form-control"><strong class=" text-muted">' . $worker_name . '</strong></div>';

	}

}
