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
class JGridElementgraphtype extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'graphtype';

	public function fetchElement( $row, $node, $group )
	{
		$graphtypes = $this->getGraphTypeList();
		$multy = trim( $node->attributes( 'multy' ) );
		/* @var $node SimpleXMLElements */
		$key = trim( $node->attributes( 'key' ) );
		$translate = trim( $node->attributes( 't' ) );
		$Text = '';
		if ( isset( $row->{$key} ) )
		{
			$Key = trim( stripslashes( $row->{$key} ) );
			switch ( $Key )
			{
				case '0':
					$Text = Text::_( 'Dinamic Graph' );
					break;

				default:
					$Text = C::_( $Key . '.LIB_TITLE', $graphtypes );
					break;
			}
		}
		if ( $multy == 1 || $multy == 'e' )
		{
			$Parts = explode( '.', $key );
			$Base = (array) C::_( C::_( '0', $Parts ), $row, array() );
			$HTML = array();
			$TT = array();
			if ( count( $Base ) )
			{
				foreach ( $Base as $Item )
				{
					$Text = C::_( C::_( C::_( '1', $Parts ), $Item ) . '.LIB_TITLE', $graphtypes, false );
					if ( empty( $Text ) )
					{
						$Text = Text::_( 'Dinamic Graph' );
					}
					if ( $translate == 1 )
					{
						$Text = XTranslate::_( $Text );
					}
					$TT[] = $Text;
					$HTML[] = '<div class="key_div">'
									. '<span class="key_val">'
									. $Text
									. '</span>'
									. '</div>'
					;
				}
				$Text = $multy == 'e' ? implode( ', ', $TT ) : '<div class="key_row">' . implode( '', $HTML ) . '</div>';
			}
		}
		else
		{
			if ( $translate == 1 )
			{
				$Text = XTranslate::_( $Text );
			}
		}
		return $Text;

	}

	public function getGraphTypeList()
	{
		static $Types = null;
		if ( is_null( $Types ) )
		{
			$Query = 'select t.id, t.lib_title from LIB_STANDARD_GRAPHS t where t.active > -1 order by t.lib_title asc';
			$Types = DB::LoadObjectList( $Query, 'ID' );
		}
		return $Types;

	}

}
