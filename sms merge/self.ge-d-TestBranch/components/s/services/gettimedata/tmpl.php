<div class = "tk_i_separator"></div>
<?php
$StartingDate = new PDate( Request::getVar( 'start' ) );
$EndingDate = new PDate( Request::getVar( 'end' ) );
$Group = Request::getInt( 'group' );
require_once PATH_BASE . DS . 'libraries' . DS . 'calendarhelper.php';
$GraphTimeData = TKCalendar::GetGraphTimesData( $Group );
echo TKCalendar::getHeader( $StartingDate, $EndingDate, '_t' );
TKCalendar::$step = 10;
TKCalendar::$countItems = 1;
foreach ( $GraphTimeData as $Time )
{
	echo TKCalendar::getTimeItems( $StartingDate, $EndingDate, $Time );
}