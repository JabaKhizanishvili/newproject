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
$Query = 'DECLARE
	p_nextval NUMBER;
BEGIN 
	EXECUTE IMMEDIATE \'drop sequence library\';
SELECT
	max(m.m)+ 1 mm
	INTO p_nextval
FROM
	(
	select max(ID) m from hrs_graph union all 
	select max(ID) m from hrs_overtime_alerts union all 
	select max(ID) m from hrs_table union all 
	select max(TASK_ID) m from hrs_tasks union all 
	select max(ID) m from hrs_transfers union all 
	select max(ID) m from hrs_workers_holiday union all 
	select max(ID) m from lib_access_manager union all 
	select max(ID) m from lib_actions union all 
	select max(ID) m from lib_app_categories union all 
	select max(ID) m from lib_applications_types union all 
	select max(ID) m from lib_categories union all 
	select max(ID) m from lib_change_type union all 
	select max(ID) m from lib_doors union all 
	select max(ID) m from lib_flow_elements union all 
	select max(ID) m from lib_graph_times union all 
	select max(ID) m from lib_holidays union all 
	select max(ID) m from lib_job_descriptions union all 
	select max(ID) m from lib_links union all 
	select max(ID) m from lib_menus union all 
	select max(ID) m from lib_msg_templates union all 
	select max(ID) m from lib_offices union all 
	select max(ID) m from lib_official_types union all 
	select max(ID) m from lib_positions union all 
	select max(ID) m from lib_release_type union all 
	select max(ID) m from lib_roles union all 
	select max(ID) m from lib_staff_schedules union all 
	select max(ID) m from lib_standard_graphs union all 
	select max(ID) m from lib_systems union all 
	select max(ID) m from lib_unitorgs union all 
	select max(ID) m from lib_units union all 
	select max(ID) m from lib_unittypes union all 
	select max(ID) m from lib_wgroups union all 
	select max(ID) m from lib_workers_groups union all 
	select max(ID) m from lib_working_rates union all 
	select max(ID) m from news union all 
	select max(ID) m from rel_worker_cert
) m;
EXECUTE immediate \'CREATE SEQUENCE library START WITH \' || p_nextval || \' INCREMENT BY 1\';
END;';

echo 'Recreate Sequesnce : ';
var_dump( DB::CallStatement( $Query ) );
echo PHP_EOL;
//
//$Query2 = 'update Hrs_Applications s
//   set s.id = procedures.nextval
// where rowid in (select max(rowid)
//                   from Hrs_Applications t
//                  group by t.id
//                 having count(1) > 1)';
//
//echo 'Update Records : ';
//var_dump( DB::CallStatement( $Query2 ) );
//echo PHP_EOL;

echo '</pre>' . PHP_EOL . PHP_EOL;
$tm = round( microtime( true ) - $st, 10 );
echo 'Time Elapsed: ' . $tm . ' Sec' . PHP_EOL . PHP_EOL;
