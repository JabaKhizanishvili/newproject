<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$config = get_object_vars( $this );
/** @var TableHrs_workersInterface $User */
//$User = $this->data->User;
/** @var TableHrs_tableInterface $Table */
$Format = Helper::getConfig( 'tabel_format', 0 );
if ( $Format )
{
	require 'default_new.php';
}
else
{
	require 'default_old.php';
}