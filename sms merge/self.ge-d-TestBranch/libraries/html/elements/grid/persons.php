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
class JGridElementPersons extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'persons';

	public function fetchElement( $row, $node, $group )
	{
		$Result = '';
		$Worker = [];
		$key = trim( $node->attributes( 'key' ) );
		$tooltip = trim( $node->attributes( 'tooltip' ) );
		$translate = trim( $node->attributes( 't' ) );
		$input = trim( $node->attributes( 'input' ) );
		$multy = trim( $node->attributes( 'multy' ) );
		$Tscope = trim( $node->attributes( 'tscope' ) );

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
					$W[] = C::_( 'FULLNAME', $each );
				}
				$W_data['FULLNAME'] = implode( ', ', $W );
			}
			else
			{
				$W_data = C::_( $id, $workerData );
			}
			if ( $tooltip )
			{
				$Worker[] = C::_( 'FULLNAME', $W_data );
			}
			else
			{
				$username = C::_( 'LDAP_USERNAME', $W_data ) ? '<span class="gray"> - ' . C::_( 'LDAP_USERNAME', $W_data ) . '</span>' : '';
				$private_number = C::_( 'PRIVATE_NUMBER', $W_data ) ? '<span class="gray"> - ' . C::_( 'PRIVATE_NUMBER', $W_data ) . '</span>' : '';
				$wname = C::_( 'FULLNAME', $W_data );
				if ( $multy == 1 )
				{
					$Worker[] = '<div class="key_div">'
									. '<span class="key_val">'
									. $wname
									. '</span>'
									. '</div>';
				}
				else
				{
					$Worker[] = $wname . $username . $private_number;
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
							. ' w.private_number, '
							. ' w.ldap_username, '
							. ' w.firstname, '
							. ' w.lastname'
							. ' from slf_persons w '
							. ' left join slf_worker sw on sw.person = w.id  '
			;
			$result = DB::LoadObjectList( $query, 'ID' );

			foreach ( $result as $key => $d )
			{
				$Text = '';
				if ( $translate == 1 )
				{
					$Text = XTranslate::_( $d->FIRSTNAME, $Tscope );
					$Text .= ' ' . XTranslate::_( $d->LASTNAME, $Tscope );
				}

				$d->FULLNAME = $Text;
			}

			$data = $result;
		}

		return $data;

	}

}
