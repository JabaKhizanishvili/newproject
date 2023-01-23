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
class JGridElementFPersons extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'fpersons';

	public function fetchElement( $row, $node, $group )
	{
		$Result = '';
		$Worker = [];
		$key = trim( $node->attributes( 'key' ) );
		$tooltip = trim( $node->attributes( 'tooltip' ) );
		$input = trim( $node->attributes( 'input' ) );
		$translate = trim( $node->attributes( 't' ) );
		$Tscope = trim( $node->attributes( 'tscope' ) );
		$multy = trim( $node->attributes( 'multy' ) );

		$Value = explode( ',', C::_( $key, $row ) );
		if ( $multy == 1 )
		{
			$Parts = explode( '.', $key );
			$Base = (array) C::_( C::_( '0', $Parts ), $row, array() );
			$HTML = array();
			if ( count( $Base ) )
			{
				foreach ( $Base as $Item )
				{
					$Text = C::_( C::_( '1', $Parts ), $Item );
					if ( $translate == 1 )
					{
						$Text = XTranslate::_( $Text );
					}
					$HTML[] = explode( ',', $Text );
				}
				$Value = $HTML;
			}
		}

		$workerData = $this->getWorker( $input, $translate, $Tscope );
		foreach ( $Value as $id )
		{
			$W_data = array();
			if ( is_array( $id ) )
			{
				$W = array();
				foreach ( $id as $k )
				{
					$each = C::_( $k, $workerData );
					$T = C::_( 'FULLNAME', $each );
					$W[] = $T;
				}
				$W_data['FULLNAME'] = implode( ', ', $W );
			}
			else
			{
				$W_data = C::_( $id, $workerData );
			}
			if ( $tooltip )
			{
				$T = C::_( 'FULLNAME', $W_data );
				$Worker[] = $T;
			}
			else
			{
				if ( $multy == 1 )
				{
					$T = C::_( 'FULLNAME', $W_data );
					if ( $translate == 1 )
					{
						$T = XTranslate::_( $T );
					}
					$Worker[] = '<div class="key_div">'
									. '<span class="key_val">'
									. $T
									. '</span>'
									. '</div>';
				}
				else
				{
					$T = C::_( 'FULLNAME', $W_data );
					$Worker[] = $T;
				}
			}
		}
		if ( $tooltip )
		{
			$Result = Helper::MakeToolTip( implode( ', ', $Worker ), 50, 0 );
		}
		elseif ( $multy == 1 )
		{
			$Result = '<div class="key_row">' . implode( '', $Worker ) . '</div>';
		}
		else
		{
			$Result = implode( '<br>', $Worker );
		}
		if ( $multy == 1 )
		{
			$Result = '<div class="key_row">' . $Result . '</div>';
		}
		return $Result;

	}

	public function getWorker( $input = '', $translate = 0, $Tscope = '' )
	{
		static $data = null;
		if ( is_null( $data ) )
		{
			$query = 'select '
							. (!empty( $input ) ? ' sw.id, ' : ' w.id, ')
							. ' w.firstname, '
							. ' w.lastname '
							. ' from slf_persons w '
//                . ' left join slf_worker sw on sw.person = w.id  '
			;
			$result = DB::LoadObjectList( $query, 'ID' );

			$collect = [];
			foreach ( $result as $key => $data )
			{
				$Text = '';
				if ( $translate == 1 )
				{
					$Text = XTranslate::_( C::_( 'FIRSTNAME', $data ), $Tscope );
					$Text .= ' ' . XTranslate::_( C::_( 'LASTNAME', $data ), $Tscope );
				}
				else
				{
					$Text = C::_( 'FIRSTNAME', $data ) . ' ' . C::_( 'LASTNAME', $data );
				}

				$collect[$key]['ID'] = $data->ID;
				$collect[$key]['FULLNAME'] = $Text;
			}

			$data = $collect;
		}

		return $data;

	}

}
