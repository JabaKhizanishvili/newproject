<?php
define( 'DS', DIRECTORY_SEPARATOR );
$base = str_replace( '/' . basename( dirname( __FILE__ ) ), '', dirname( __FILE__ ) );
define( 'PATH_BASE', $base );
session_start();
include(PATH_BASE . DS . 'define.php');
include(PATH_BASE . DS . 'include.php');

require_once PATH_BASE . DS . 'components' . DS . 'workerholiday' . DS . 'table.php';

$st = microtime( 1 );
$Query = 'select * from hrs_applications t '
				. ' where t.status > -1 '
				. ' and t.type=7 '
				. ' and t.day_count = 0 '
;
$items = DB::LoadObjectList( $Query );
//echo 'Run SMS Script<pre>' . PHP_EOL;
if ( count( $items ) )
{
	$Table = new HolidayTable();
	foreach ( $items as $data )
	{
		$data = (array) $data;
		$Table->resetAll();
		if ( $data['DAY_COUNT'] > 0 )
		{
			continue;
		}
		$data['DAY_COUNT'] = Helper::CalculateDayCount( C::_( 'START_DATE', $data ), C::_( 'END_DATE', $data ) );
		$Table->bind( $data );
		$Table->store();
		echo C::_( 'ID', $data ) . '- Done!' . PHP_EOL;
	}

	echo '</pre>' . PHP_EOL . PHP_EOL;
	$tm = round( microtime( true ) - $st, 10 );
	echo 'Time Elapsed: ' . $tm . ' Sec' . PHP_EOL . PHP_EOL;
}
echo 'Done!';
echo PHP_EOL;
