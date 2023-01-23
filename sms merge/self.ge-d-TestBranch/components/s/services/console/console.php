<?php

class console
{
	public function GetService()
	{
		$command = trim( Request::getVar( 'command' ) );
		$name = explode( '(', $command );
		$params = [];
		if ( empty( $name[1] ) )
		{
			return $this->ECHO( $command . ' - undefined!' );
		}

		$Params = explode( ')', $name[1] );
		if ( !empty( $Params[0] ) )
		{
			foreach ( explode( ',', $Params[0] ) as $val )
			{
				$params[] = trim( str_replace( '"', '', $val ) );
			}
		}

		$function = str_replace( ' ', '_', $name[0] );
		if ( !method_exists( 'console', $function ) )
		{
			return $this->ECHO( $command . ' - Undefined request!' );
		}

		$status = $this->$function( $params );
		return $this->ECHO( $status );

	}

	public function ECHO( $msg )
	{
		$Response = new stdClass();
		$Response->status = $msg;
		return json_encode( $Response );

	}

	public function table()
	{
		$url = 'NEW_TAB=';
		$url .= 'https://' . $_SERVER['SERVER_NAME'] . '/tables/update.php';
		return $url;

	}

	public function cron( $params = [] )
	{
		if(empty($params))
		{
			return 'cron(...) - Undefined name of cron!';
		}
		$name = C::_( '0', $params );
		$url = 'NEW_TAB=';
		$url .= 'https://' . $_SERVER['SERVER_NAME'] . '/cron/' . $name . '.php';
		return $url;

	}

	public function gziro()
	{
		$url = 'NEW_TAB=';
		$url .= 'http://localhost/';
		return $url;

	}

}
