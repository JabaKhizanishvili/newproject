<?php
if ( Users::isLogged() )
{
	require_once '_main.php';
}
else
{
	require_once '_login.php';
}
