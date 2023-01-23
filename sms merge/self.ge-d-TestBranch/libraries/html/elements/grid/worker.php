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
class JGridElementWorker extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'Worker';

	public function fetchElement( $row, $node, $group )
	{
		$key = trim( $node->attributes( 'key' ) );
		$directchief_id = $row->DIRECTCHIEF;
		$Worker = $this->getWorker( $directchief_id );

		$Value = C::_( $key, $row );

		$Data = $Worker->FULLNAME;
		return $Data;

	}

	public function getWorker( $worker_id )
	{
		$query = 'select '
						. ' id, '
						. ' w.firstname || \' \' || w.lastname fullname '
						. ' from slf_persons w '
						. ' where w.id = ' . $worker_id;
		$WorkerFullName = DB::LoadObject( $query );
		return $WorkerFullName;

	}

}
