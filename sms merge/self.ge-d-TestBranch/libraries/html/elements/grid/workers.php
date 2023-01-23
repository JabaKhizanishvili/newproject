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
class JGridElementWorkers extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'Workers ';

	public function fetchElement( $row, $node, $config )
	{
		$html = '';
		$key = trim( $node->attributes( 'key' ) );
		$id = trim( $node->attributes( 'id' ) );
		$function = Request::getCmd( 'js' );
		$translate = trim( $node->attributes( 't' ) );
		$Tscope = trim( $node->attributes( 'tscope' ) );
		$Vars = Request::getVar( 'jsvar', array() );
		if ( $key )
		{
			$VarsData = '';
			if ( count( $Vars ) )
			{
				$VarsData = ',' . implode( ',', $Vars );
			}

			$Text = [];
			$input = (array) explode( ', ', $key );
			foreach ( $input as $key )
			{
				$label = $row->{strtoupper( trim( $key ) )};
				if ( $translate == 1 )
				{
					$Text[] = XTranslate::_( $label, $Tscope );
				}
			}

			if ( !empty( $Text ) )
			{
				$html .= ' <a href="javascript:window.parent.' . $function . '(' . $row->$id . $VarsData . ');window.parent.$.prettyPhoto.close();" >';
				$html .= implode( ' ', $Text );
				$html .= ' </a>';
				$html .= ' <button class="btn btn-primary" type="button" style="float:right;width:auto;" onclick="javascript:window.parent.' . $function . '(' . $row->$id . $VarsData . ');window.parent.$.prettyPhoto.close();" >' . Text::_( 'Chouse' ) . '</button><div class="cls"></div>';
			}
		}
		return $html;

	}

}
