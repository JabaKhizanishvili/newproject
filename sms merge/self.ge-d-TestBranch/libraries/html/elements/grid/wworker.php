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
class JGridElementWWorker extends JGridElement
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
		$translate = trim( $node->attributes( 't' ) );
		$Tscope = trim( $node->attributes( 'tscope' ) );
		$value = C::_( $key, $row );
		$Data = '';
		if ( $value )
		{
			$Workers = $this->getWorker();
			$Worker = C::_( $value, $Workers );
			$Text = '';
			if ( $translate == 1 )
			{
				$Text = XTranslate::_( C::_( 'FIRSTNAME', $Worker ), $Tscope ) . ' ' . XTranslate::_( C::_( 'LASTNAME', $Worker ), $Tscope );
			}
			else
			{
				$Text = C::_( 'FIRSTNAME', $Worker ) . ' ' . C::_( 'LASTNAME', $Worker );
			}
			$Data = $Text;
		}
		return $Data;

	}

	public function getWorker()
	{
		static $Data = null;
		if ( is_null( $Data ) )
		{
			$query = 'select '
							. ' id, '
							. ' w.firstname, '
							. ' w.lastname '
							. ' from slf_persons w ';
			$Data = DB::LoadObjectList( $query, 'ID' );
		}
		return $Data;

	}

}
