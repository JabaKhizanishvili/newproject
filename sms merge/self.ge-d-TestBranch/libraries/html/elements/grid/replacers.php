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
class JGridElementReplacers extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'replacers';

	public function fetchElement( $row, $node, $group )
	{
		$key = trim( $node->attributes( 'key' ) );
		$t = trim( $node->attributes( 't' ) );
		$Tscope = trim( $node->attributes( 'tscope' ) );
		$value = C::_( $key, $row );
		$id = C::_( 'ID', $row );
		if ( empty( $value ) )
		{
			return;
		}

		$options = array();
		$pids = (array) explode( ',', $value );
		$options[] = HTML::_( 'select.option', '-1', Text::_( 'select category' ) );
		foreach ( $pids as $pid )
		{
			$person = $this->Persons( $pid );
			$Text = '';
			if ( $t == 1 )
			{
				$Text = XTranslate::_( $person->FIRSTNAME, $Tscope ) . ' ' . XTranslate::_( $person->LASTNAME, $Tscope );
			}
			else
			{
				$Text = $person->FIRSTNAME . ' ' . $person->LASTNAME;
			}

			$options[] = HTML::_( 'select.option', $pid, $Text );
		}

		$control_name = 'params';
		return HTML::_( 'select.genericlist', $options, '' . $control_name . '[' . $key . '][' . $id . ']', ' class="form-control" ', 'value', 'text', $value, $control_name . $key );

	}

	public function Persons( $id = 0 )
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

		if ( $id > 0 )
		{
			return C::_( $id, $result );
		}

		return $result;

	}

}
