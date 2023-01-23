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
class JGridElementFileprint extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'fileprint';

	public function fetchElement( $row, $node, $group )
	{
		/* @var $node SimpleXMLElements */
		$key = trim( $node->attributes( 'key' ) );
		$LimitType = trim( $node->attributes( 'limit_type' ) );
		$Length = intval( $node->attributes( 'length' ) );
		$Text = '';
		if ( isset( $row->{$key} ) )
		{
			if ( !is_array( $row->{$key} ) )
			{
				$Text = explode( '|', trim( stripslashes( $row->{$key} ) ) );
			}
			else
			{
				$Text = $row->{$key};
			}

			foreach ( $Text as $key => $val )
			{
				$href = 'download/?f=' . $val;
				$txt = substr( $val, strpos( $val, "_" ) + 1 );
				$Text[$key] = '<a href="' . $href . '">' . $txt . '</a>';
			}
			$Text = implode( '<br> ', $Text );
//			if ( strlen( $Text ) > 25 )
//			{
//				switch ( $LimitType )
//				{
//					case 1:
//						$Text = Helper::MakeToolTip( $Text, $Length, 0 );
//						break;
//					case 2:
//						$Text = Helper::MakeToolTip( $Text, $Length, 1 );
//						break;
//				}
//			}
		}
		return $Text;

	}

}
