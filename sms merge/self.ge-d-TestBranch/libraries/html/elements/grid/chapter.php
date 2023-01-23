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
class JGridElementChapter extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'Chapter';

	public function fetchElement( $row, $node, $group )
	{
		$Chapters = SalaryHelper::getChapterList();
		/* @var $node SimpleXMLElements */
		$key = trim( $node->attributes( 'key' ) );
		$LimitType = trim( $node->attributes( 'limit_type' ) );
		$Length = intval( $node->attributes( 'length' ) );
		$Text = '';
		if ( isset( $row->$key ) )
		{
			$Key = trim( stripslashes( $row->$key ) );
			$Text = C::_( $Key . '.LIB_TITLE', $Chapters );
			switch ( $LimitType )
			{
				case 1:
					$Text = Helper::MakeToolTip( $Text, $Length, 0 );
					break;
				case 2:
					$Text = Helper::MakeToolTip( $Text, $Length, 1 );
					break;
			}
			return $Text;
		}

	}

}
