<?php

// Created by Irakli Gzirishvili 26-10-2021.

class JGridElementDate extends JGridElement
{
	protected $_name = 'date';

	public function fetchElement( $row, $node, $group )
	{
		/* @var $node SimpleXMLElements */
		$key = trim( $node->attributes( 'key' ) );
		$format = $node->attributes( 'format' );
		$multy = trim( $node->attributes( 'multy' ) );
		$Text = null;
		if ( isset( $row->{$key} ) )
		{
			$Text = PDate::Get( C::_( $key, $row ) )->toFormat( $format );
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
					$Text = PDate::Get( C::_( C::_( '1', $Parts ), $Item ) )->toFormat( $format );
					$TT[] = $Text;
					$HTML[] = '<div class="key_div">'
									. '<span class="key_val">'
									. $Text
									. '</span>'
									. '</div>'
					;
				}
			}
			$Text = $multy == 'e' ? implode( ', ', $TT ) : '<div class="key_row">' . implode( '', $HTML ) . '</div>';
		}

		return $Text;

	}

}
