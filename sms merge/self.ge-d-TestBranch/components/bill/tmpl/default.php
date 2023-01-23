<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$Format = Helper::getConfig( 'tabel_format', 0 );
if ( $Format )
{
	require 'default_new.php';
}
else
{
	require 'default_old.php';
}