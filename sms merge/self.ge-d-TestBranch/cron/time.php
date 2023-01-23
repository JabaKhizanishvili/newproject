<?php
define( 'DS', DIRECTORY_SEPARATOR );
$base = str_replace( '/' . basename( dirname( __FILE__ ) ), '', dirname( __FILE__ ) );
define( 'PATH_BASE', $base );
session_start();
include(PATH_BASE . DS . 'define.php');
include(PATH_BASE . DS . 'include.php');

//$Config = Helper::getConfig( 'run_sms' );
//if ( !$Config )
//{
//	die( 'Run SMS Disabled By Config!' );
//}
echo '<pre><pre>';

require_once PATH_BASE . DS . 'libraries' . DS . 'live.php';
include_once BASE_PATH . DS . 'libraries' . DS . 'sms' . DS . 'oneway.php';
//@TODO
//die;
$st = microtime( 1 );
$Query = 'select * from LIB_GRAPH_TIMES t '
				. ' where t.active > -1 '
				. ' and t.type = 0 '
				. ' and t.working_time <= 0'
;
$items = DB::LoadObjectList( $Query );
//echo 'Run SMS Script<pre>' . PHP_EOL;
$T = new XHRSTable();
if ( count( $items ) )
{

	if ( !empty( $items ) )
	{
		$Table = new TableLib_graph_timesInterface( 'lib_graph_times', 'ID' );

		foreach ( $items as $data )
		{
			$data = (array) $data;
			$Table->resetAll();
			$RestType = (int) C::_( 'REST_TYPE', $data );
			if ( $RestType == 1 )
			{
				$data['REST_TIME'] = $T->CalculateHoursDiff( C::_( 'START_BREAK', $data ), C::_( 'END_BREAK', $data ) );
				$data['REST_MINUTES'] = 0;
			}
			elseif ( $RestType == 4 )
			{
				$Min = (int) C::_( 'REST_MINUTES', $data );
				$data['REST_TIME'] = round( $Min / 60, 2 );
			}
			else
			{
				$data['REST_MINUTES'] = 0;
				$data['REST_TIME'] = 0;
				$data['END_BREAK'] = '';
				$data['START_BREAK'] = '';
			}

			$data['WORKING_TIME'] = $T->CalculateHoursDiff( C::_( 'START_TIME', $data ), C::_( 'END_TIME', $data ) ) - $data['REST_TIME'];

			$Table->bind( $data );
			$Table->store();
			echo C::_( 'LIB_TITLE', $data ) . '- Done!' . PHP_EOL;
		}
	}

//$Query = 'select * from LIB_GRAPH_TIMES t '
//				. ' where t.active > -1 '
//				. ' and (t.START_TIME like ' . DB::Quote( '24%' ) . ' or t.END_TIME' . DB::Quote( '24%' ) . ' )'
//;
//$P = DB::LoadObjectList( $Query );
//echo '<pre><pre>';
//print_r( $P );
//echo '</pre><b>FILE:</b> ' . __FILE__ . '     <b>Line:</b> ' . __LINE__ . '</pre>' . "\n";

	echo '</pre>' . PHP_EOL . PHP_EOL;
	$tm = round( microtime( true ) - $st, 10 );
	echo 'Time Elapsed: ' . $tm . ' Sec' . PHP_EOL . PHP_EOL;
}
