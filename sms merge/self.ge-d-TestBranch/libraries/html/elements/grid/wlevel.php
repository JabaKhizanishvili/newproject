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
class JGridElementwlevel extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'wlevel';

	public function fetchElement( $row, $node, $group )
	{
		$key = trim( $node->attributes( 'key' ) );
		$wlevels = $this->getwlevelList();

		$Value = C::_( $key, $row );
		$Color = C::_( $Value . '.COLOR', $wlevels );
		$Text = '<div style="width: 100px; margin:0px auto; text-align:center; line-height:20px;background:' . $Color . ';">' . C::_( $Value . '.TITLE', $wlevels ) . '</div>';
		return $Text;

	}

	public function getwlevelList()
	{
		static $wlevels = null;
		if ( is_null( $wlevels ) )
		{
			$query = 'select '
							. ' id, '
							. ' t.lib_title title,'
							. ' t.color '
							. ' from lib_levels t ';
			$wlevels = DB::LoadObjectList( $query, 'ID' );
		}
		return $wlevels;

	}

}
