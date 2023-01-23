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
class JGridElementWorkerStatus extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'WorkerStatus';

	public function fetchElement( $row, $node, $group )
	{
		/* @var $node SimpleXMLElements */
		$Text = '';
		$WorkerTime = C::_( 'WORK_TIME', $row );
		$AppType = C::_( 'APPTYPE', $row );
		$REAL_TYPE_ID = C::_( 'REAL_TYPE_ID', $row );
		if ( $WorkerTime > 0 && ($REAL_TYPE_ID == 1 || $REAL_TYPE_ID == 11) || $REAL_TYPE_ID == 1 )
		{
			return '<span class="green">' . Text::_( 'User is at work' ) . '</span>';
		}
		else if ( ($REAL_TYPE_ID != 1 || $REAL_TYPE_ID != 11) and $AppType > -1 and $AppType != 10 )
		{
			return Helper::MakeDoubleToolTip(
											'<span class="red">' . Text::_( 'User is not at work' ) . '</span>'
											, '<span class="col-sm-12"><strong>' . C::_( 'APP_NAME', $row ) . '</strong></span>'
											. '<span class="col-sm-12">' . Text::_( 'INFO START DATE' ) . ' : ' . C::_( 'START_DATE', $row ) . '</span>'
											. '<span class="col-sm-12">' . Text::_( 'INFO END DATE' ) . ' : ' . C::_( 'END_DATE', $row ) . '</span>'
			);
		}
		else if ( $WorkerTime > 0 && $REAL_TYPE_ID == 2 )
		{
			return '<span class="yellow">' . Text::_( 'User is at working but not on place' ) . '</span>';
		}
		else if ( $WorkerTime > 0 && $REAL_TYPE_ID == 10 )
		{
			return '<span class="purple">' . Text::_( 'User is on sportHall' ) . '</span>';
		}
		else
		{
			return Helper::MakeDoubleToolTip(
											'<span class="red">' . Text::_( 'User is not at work' ) . '</span>'
											, '<span class="col-sm-12"><strong>' . Text::_( 'Not Working hours!' ) . '</strong></span>'
			);
		}



		if ( $AppType == -1 || $AppType == 10 )
		{
			
		}
		else
		{
			$Text .= Helper::MakeDoubleToolTip(
											'<span class="red">' . Text::_( 'User is not at work' ) . '</span>'
											, '<span class="col-sm-12"><strong>' . C::_( 'APP_NAME', $row ) . '</strong></span>'
											. '<span class="col-sm-12">' . Text::_( 'INFO START DATE' ) . ' : ' . C::_( 'START_DATE', $row ) . '</span>'
											. '<span class="col-sm-12">' . Text::_( 'INFO END DATE' ) . ' : ' . C::_( 'END_DATE', $row ) . '</span>'
			);
//			$Text .= Text::_( 'User is not at work' );
//			$Text .= C::_( 'APP_NAME', $row );
		}
		return $Text;

	}

}
