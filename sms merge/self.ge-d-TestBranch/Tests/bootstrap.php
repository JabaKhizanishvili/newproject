<?php
define( 'DS', DIRECTORY_SEPARATOR );
define( 'PATH_BASE', dirname( dirname( __FILE__ ) ) );
include(PATH_BASE . DS . 'define.php');
include(PATH_BASE . DS . 'libraries' . DS . 'x.php');

class PHPUnit_Framework_TestCase extends \PHPUnit\Framework\TestCase
{
	
}
