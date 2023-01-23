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
class JGridElementSchgworkers extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'schgworkers';

	public function fetchElement( $row, $node, $config )
	{
		$html = '';
		$translate = trim( $node->attributes( 't' ) );
		$key = trim( $node->attributes( 'key' ) );
		$Tscope = trim( $node->attributes( 'tscope' ) );

		if ( $key )
		{
			if ( isset( $row->ID ) )
			{
				$workers = $this->getWorkers( $row->ID, $translate, $Tscope );
				$html = Helper::MakeToolTip( $workers, 50, 0 );
			}
		}

		return $html;

	}

	public function getWorkers( $Group, $translate = 1, $Tscope = '' )
	{
		static $WorkersData = null;
		if ( is_null( $WorkersData ) )
		{
			$Query = 'select '
							. ' wg.group_id, '
							. ' w.firstname, '
							. ' w.lastname '
							. ' from hrs_workers_sch w '
							. ' left join rel_workers_groups wg on w.id = wg.worker '
							. ' where '
							. ' w.active > 0 '
							. ' and w.graphtype = 0 '
							. 'order by wg.ordering asc, w.firstname ';
			$Data = XRedis::getDBCache( 'lib_workers_groups', $Query );
			$WorkersData = [];
			foreach ( $Data as $data )
			{
				$key = C::_( 'GROUP_ID', $data );
				$WorkersData[$key] = C::_( $key, $WorkersData, [] );
				if ( $translate == 1 )
				{
					$WorkersData[$key][] = XTranslate::_( C::_( 'FIRSTNAME', $data ), $Tscope ) . ' ' . XTranslate::_( C::_( 'LASTNAME', $data ), $Tscope );
				}
				else
				{
					$WorkersData[$key][] = C::_( 'FIRSTNAME', $data ) . ' ' . C::_( 'LASTNAME', $data );
				}
			}
		}
		return implode( ', ', C::_( $Group, $WorkersData, [] ) );

	}

}
