<?php
define( 'DS', DIRECTORY_SEPARATOR );
$base = str_replace( '/' . basename( dirname( __FILE__ ) ), '', dirname( dirname( dirname( __FILE__ ) ) ) );

define( 'PATH_BASE', $base );
error_reporting( E_ALL );
ini_set( 'display_errors', 1 );
session_start();
include(PATH_BASE . DS . 'define.php');
include(PATH_BASE . DS . 'include.php');

echo '<pre><pre>';

$st = microtime( 1 );
$Query = 'DROP SEQUENCE EVENTS_SQS';

echo 'Delete Sequesnce : ';
var_dump( DB::CallStatement( $Query ) );
echo PHP_EOL;

$Query1 = 'declare
      n_seq number := 0;
    begin
      select max(t.id) + 1 into n_seq from Hrs_staff_events t;
      execute immediate (\'create sequence EVENTS_SQS minvalue 1 maxvalue 9999999999999999999999999999 start with \' ||
                        n_seq || \' increment by 1 cache 20\');
    end;';

echo 'Create Sequesnce : ';
var_dump( DB::CallStatement( $Query1 ) );
echo PHP_EOL;

$Query2 = 'update Hrs_staff_events s
   set s.id = EVENTS_SQS.nextval
 where rowid in (select max(rowid)
                   from Hrs_staff_events t
                  group by t.id
                 having count(1) > 1)';

echo 'Update Records : ';
var_dump( DB::CallStatement( $Query2 ) );
echo PHP_EOL;

echo '</pre>' . PHP_EOL . PHP_EOL;
$tm = round( microtime( true ) - $st, 10 );
echo 'Time Elapsed: ' . $tm . ' Sec' . PHP_EOL . PHP_EOL;
