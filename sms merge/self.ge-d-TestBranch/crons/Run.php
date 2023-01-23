<?php
define( 'DS', DIRECTORY_SEPARATOR );
$base = str_replace( '/' . basename( dirname( __FILE__ ) ), '', dirname( __FILE__ ) );
define( 'PATH_BASE', $base );
session_start();
include(PATH_BASE . DS . 'define.php');
include(PATH_BASE . DS . 'include.php');
$st = microtime( 1 );

class XCron extends XObject
{
	private $_Instances = [];
	private $_CronDir = null;
	private $_Minutes = [ 1, 2, 3, 4, 5, 6, 10, 12, 15, 20, 30 ];

	public function __construct()
	{
		$this->_CronDir = dirname( __FILE__ ) . DS . 'items';

	}

	public function Run()
	{
		$this->SetCrons();
		$CurrentMinute = (int) 0; //PDate::Get()->toFormat( '%M' );
		foreach ( $this->_Minutes as $Minute )
		{
			If ( $CurrentMinute == 0 )
			{
				$this->RunZero();
				return true;
			}

			if ( $Minute > $CurrentMinute )
			{
				break;
			}
		}
		return true;

	}

	public function GetCrons()
	{
		return Folder::files( $this->_CronDir, '\.php' );

	}

	public function SetCrons()
	{
		$Crons = $this->GetCrons();
		foreach ( $Crons as $Cron )
		{
			require_once $this->_CronDir . DS . $Cron;
			$Class = 'X' . File::stripExt( $Cron );
			$this->_Instances[] = $Class::GetInstance();
		}
		return true;

	}

	public function RunZero()
	{
		foreach ( $this->_Minutes as $Minute )
		{
			foreach ( $this->_Instances as $Instance )
			{
				$Method = 'RunOn' . $Minute;
				$Instance->{$Method}();
			}
		}
		return true;

	}

	public function __call( $Name, $Args = [] )
	{
		echo $Name . ' - Pass! Arguments : ' . implode( ', ', $Args ) . PHP_EOL;
		return true;

	}

}

echo '<pre><pre>';

/** @var XCron $Cron */
$Cron = XCron::GetInstance();
$Cron->Run();
$tm = round( microtime( true ) - $st, 10 );
echo ' Time Elapsed: ' . $tm . ' Sec';
