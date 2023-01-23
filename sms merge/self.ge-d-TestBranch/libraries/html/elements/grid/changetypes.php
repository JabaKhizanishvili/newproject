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
class JGridElementChangetypes extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'changetypes';

	public function fetchElement( $row, $node, $group )
	{
		$key = trim( $node->attributes( 'key' ) );
		$directchief_id = C::_( $key, $row );
		$Data = '';
		if ( $directchief_id )
		{

			$Worker = $this->getData( $directchief_id );
			$Data = C::_( 'NAME', $Worker );
		}
		return $Data;

	}

	public function getData( $id )
	{
		$query = 'select '
						. ' id, '
						. ' w.lib_title name '
						. ' from LIB_CHANGE_TYPE w '
						. ' where w.id = ' . $id;
		return DB::LoadObject( $query );

	}

}
