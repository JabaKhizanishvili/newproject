<?php
define( 'DS', DIRECTORY_SEPARATOR );
$base = realpath( dirname( __FILE__ ) . DS . '..' . DS );

define( 'PATH_BASE', $base );
error_reporting( E_ALL );
ini_set( 'error_log', PATH_LOGS . DS . 'Table.log.txt' );
include(PATH_BASE . DS . 'define.php');
include(PATH_BASE . DS . 'include.php');
require_once './helper.php';

$Tables = TableHelper::getTableList();
$XMLTables = TableHelper::getXMLTablesList();

TableHelper::ProcessTable2XML( $XMLTables, $Tables );
