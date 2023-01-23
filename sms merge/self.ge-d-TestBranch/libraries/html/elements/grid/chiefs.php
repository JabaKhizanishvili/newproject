<?php
require_once PATH_BASE . DS . 'libraries' . DS . 'Units.php';

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
class JGridElementChiefs extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'chiefs';

	public function fetchElement( $row, $node, $group )
	{
		$key = trim( $node->attributes( 'key' ) );
		$org = trim( $node->attributes( 'org' ) );
		$translate = trim( $node->attributes( 't' ) );
		$Tscope = trim( $node->attributes( 'tscope' ) );

		$key_val = isset( $row->{$key} ) ? $row->{$key} : '';
		$org_val = isset( $row->{$org} ) ? $row->{$org} : '';

		$Select = $this->Select( $key_val, $org_val, $key );
		$collect = [];
		foreach ( $Select as $key => $value )
		{
			$collect[$value->ORG][] = $value->CHIEF;
		}

		$Persons = $this->Persons();
		$result = '';
		foreach ( $collect as $key => $value )
		{
			if ( $translate == 1 )
			{
				$key = XTranslate::_( $key );
			}
			$result .= '<strong style="color:#00756A;">' . $key . '</strong><br>';
			$chiefs = [];
			foreach ( $value as $val )
			{
				$Text = '';
				if ( $translate == 1 )
				{
					$Text = XTranslate::_( C::_( $val . '.FIRSTNAME', $Persons ), $Tscope ) . ' ' . XTranslate::_( C::_( $val . '.LASTNAME', $Persons ), $Tscope );
				}
				else
				{
					$Text = C::_( $val . '.FIRSTNAME', $Persons ) . ' ' . C::_( $val . '.LASTNAME', $Persons );
				}
				$chiefs[] = $Text;
			}
			$result .= '<div class="key_div">'
							. '<span class="key_val">'
							. implode( ', ', $chiefs )
							. '</span>'
							. '</div>';
		}
		return $result;

	}

	public function Select( $id = 0, $org = 0, $key = 'worker_pid' )
	{
		static $result_chiefs = null;
		if ( is_null( $result_chiefs ) )
		{
			$skey = '';
			if ( $key == 'PERSON' )
			{
				$skey = ' t.worker_pid ';
			}
			if ( $key == 'WORKER' )
			{
				$skey = ' t.worker ';
			}
			if ( $key == 'ORGPID' )
			{
				$skey = ' t.worker_opid ';
			}
			$query = 'select '
							. ' o.id orgid, '
							. ' o.lib_title org, '
							. ' t.chief_pid chief, '
							. $skey . '  worker '
							. ' from rel_worker_chief t '
							. ' left join lib_unitorgs o on o.id = t.org '
							. ' where '
							. ' t.clevel in (0, 1) '
			;
			$load = XRedis::getDBCache( 'rel_worker_chief', $query );
//			$load = DB::LoadObjectList( $query );
			$collect = [];
			foreach ( $load as $data )
			{
				$collect[$data->WORKER . '|' . $data->ORGID][] = $data;
			}

			$result_chiefs = $collect;
		}

		$result = C::_( $id . '|' . $org, $result_chiefs );
		return $result;

	}

	public function Persons()
	{
		static $result = null;
		if ( is_null( $result ) )
		{
			$query = 'select '
							. ' w.id, '
							. ' w.firstname, '
							. ' w.lastname '
							. ' from slf_persons w '
			;
			$result = DB::LoadObjectList( $query, 'ID' );
		}
		return $result;

	}

}
