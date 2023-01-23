<?php
define( 'DS', DIRECTORY_SEPARATOR );
define( 'PATH_BASE', dirname( __FILE__ ) );

session_start();
global $GLOBALTIME;
$GLOBALTIME = microtime( true );

include('define.php');
include('include.php');

XSystem::InitSystem();

XTranslate::SetLanguage();
//Init User
Users::InitUser();
$layout = trim( Request::getCmd( 'tmpl', '' ) );
switch ( $layout )
{
	case 'modal':
		$layout = 'modal';
		$GLOBAL_CONTENT = XSystem::RenderContent();
		break;
	case 'off':
		$GLOBAL_CONTENT = Helper::SetJSVars();
		$layout = 'off';
		break;
	default:
		$layout = 'index';
		$GLOBAL_CONTENT = XSystem::RenderContent();
		break;
}
$layout_file = X_PATH_TEMPLATE . DS . $layout . '.php';
if ( !is_file( $layout_file ) )
{
	$layout_file = X_PATH_TEMPLATE . DS . 'index.php';
}
require_once X_PATH_TEMPLATE . DS . 'helper.php';
require_once $layout_file;
