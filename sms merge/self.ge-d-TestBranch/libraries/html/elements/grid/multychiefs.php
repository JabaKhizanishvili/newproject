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
class JGridElementMultychiefs extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'multychiefs';

	public function fetchElement( $row, $node, $group )
	{
		/* @var $node SimpleXMLElements */
		$key = 'PERSON';
		$Mkey = trim( $node->attributes( 'key' ) );
		$Row = C::_( $Mkey, $row );
		$Key = C::_( '0.' . $key, $Row );
		$translate = trim( $node->attributes( 't' ) );
		$graphtypes = [];
		foreach ( $Row as $rr )
		{
			$org = C::_( 'ORG', $rr );
			$pchiefs = $this->getPerson( C::_( 'CHIEFS', $rr ) );
			$chfss = Xhelp::getWorkerChiefs( $Key, $org );
			$chfs = array_merge( $chfss, $pchiefs );
			if ( !count( $chfs ) )
			{
				continue;
			}
			$graphtypes[$org]['ORG'] = XTranslate::_( C::_( 'ORG_NAME', $rr ) );
			$graphtypes[$org]['DATA'] = $chfs;
		}
		ksort( $graphtypes );

		$Text = '';
		$HTML = array();
		if ( count( $graphtypes ) )
		{
			foreach ( $graphtypes as $ORG => $chiefs )
			{
				$Text = implode( ', ', $chiefs['DATA'] ) . '. ';
				$HTML[] = '<strong>' . $chiefs['ORG'] . ': </strong>'
								. '<div class="key_div">'
								. '<span class="key_val">'
								. $Text
								. '</span>'
								. '</div>'
				;
			}
			$Text = '<div class="key_row ">' . implode( '', $HTML ) . '</div>';
		}
		return $Text;

	}

	public function getPerson( $worker_id = '' )
	{
		$ids = explode( ',', $worker_id );
		static $persons = [];
		$query = 'select '
						. ' w.id, '
						. ' w.firstname || \' \' || w.lastname fullname '
						. ' from slf_persons w '
						. ' where w.active = 1 '
		;
		if ( !count( $persons ) )
		{
			$result = DB::LoadObjectList( $query );
			$collect = [];
			foreach ( $result as $value )
			{
				$collect[$value->ID] = XTranslate::_( $value->FULLNAME );
			}
			$persons = $collect;
		}
		$return = [];
		foreach ( $ids as $val )
		{
			$return[] = C::_( $val, $persons );
		}
		return $return;

	}

}
