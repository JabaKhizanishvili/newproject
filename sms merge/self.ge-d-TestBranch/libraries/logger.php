<?php

// no direct access
//Logging Class
class XLogger extends XObject
{
	// define log file  
	protected $log_dir = '/';
	protected $log_file = 'logfile';
	private $fp = NULL;

	public function __construct( $file = null, $dir = null )
	{
		if ( empty( $dir ) )
		{
			$dir = X_PATH_LOGS;
			$this->SetLogDir( $dir );
		}
		$this->SetLogFile( $file );

	}

	/**
	 * @deprecated since version 0.01
	 * @param type $message
	 * @param type $mode
	 */
	public function lwrite( $message, $mode = true )
	{
		$this->Logwrite( $message, $mode );

	}

	public function Logwrite( $message, $mode = true )
	{
		if ( !$this->fp )
		{
			$this->lopen();
		}
		$time = PDate::Get( 'now' )->format( 'Y-m-d  H:i:s', true );
		if ( $mode )
		{
			return fwrite( $this->fp, $time . ' - ' . $message . PHP_EOL );
		}
		else
		{
			return fwrite( $this->fp, $message . PHP_EOL );
		}

	}

	private function lopen()
	{
		if ( !Folder::exists( $this->log_dir ) )
		{
			Folder::create( $this->log_dir, 0777 );
		}
		$lfile = $this->log_dir . DS . $this->log_file;
		$today = date( 'Y-m-d' );
		$this->fp = fopen( $lfile . '_' . $today . '.log', 'a' ) or exit( 'Can\'t open ' . $lfile . '!' );

	}

	public function SetLogDir( $dir )
	{
		if ( !empty( $dir ) )
		{
			$this->log_dir = $dir;
			$this->fp = NULL;
		}

	}

	public function SetLogFile( $file )
	{
		if ( !empty( $file ) )
		{
			$this->log_file = $file;
			$this->fp = NULL;
		}

	}

	public function __destruct()
	{
		if ( is_resource( $this->fp ) )
		{
			fclose( $this->fp );
			$this->fp = null;
		}

	}

	public function getLogDir()
	{
		return $this->log_dir;

	}

}

/**
 * @deprecated since version 0.01
 */
class Logger extends XLogger
{
	
}
