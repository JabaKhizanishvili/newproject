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
class JGridElementLoopComments extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'LoopComments';

	public function fetchElement( $row, $node, $group )
	{

		/* @var $node SimpleXMLElements */
		$key = trim( $node->attributes( 'key' ) );
		$Text = '';
		$StartDate = Request::getVar( 'start_date', false );
		$EndDate = Request::getVar( 'end_date', false );

		if ( isset( $row->{$key} ) )
		{
			$s = json_decode( $row->{$key}, true );
			$Text = '';
			$commentData = self::getFlowComments( $s, $StartDate, $EndDate );

			foreach ( $commentData as $data )
			{
				$startDate = C::_( 'START_DATE', $data );
				$endDate = C::_( 'END_DATE', $data );
				$dayCount = C::_( 'DAY_COUNT', $data );
				$comment = C::_( 'UCOMMENT', $data );
//				$Text .= Text::_( 'HOLIDAY START G' ) .': <b>' . $startDate . '</b>; '. Text::_( 'HOLIDAY END G' ) .': <b>' . $endDate  . '</b>; '. Text::_( 'DAY COUNT G' ) .': <b>' . $dayCount. '</b><br>'  . Text::_( 'COMMENT' ) .': <b>' . $comment. '</b><br><br>';
				$Text .= '<br>' . $comment. '<br><br>';
			}
			return $Text;
		}

	}

	public static function getFlowComments( $Worker, $StartDate, $EndDate )
	{
		$Query = 'select '
						. ' to_char(t.start_date, \'dd-mm-yyyy\') start_date, '
						. ' to_char(t.end_date, \'dd-mm-yyyy\') end_date, '
						. ' t.day_count, '
						. ' t.ucomment '
						. ' from  HRS_APPLICATIONS t '
						. ' where '
						. ' worker =  ' . (int) $Worker
						. ' and t.type = 5 '
						. ' and t.Ucomment is not null '
						. ' and t.start_date >= to_date(' . DB::Quote( PDate::Get( $StartDate )->toFormat( '%d-%m-%Y' ) ) . ', \'dd-mm-yyyy\') '
						. ' and t.start_date <= to_date(' . DB::Quote( PDate::Get( $EndDate )->toFormat( '%d-%m-%Y' ) ) . ', \'dd-mm-yyyy\') '
						. ' order by t.start_date desc '
		;

		return DB::LoadObjectList( $Query );

	}

}
