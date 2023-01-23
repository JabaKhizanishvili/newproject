<?php
defined( 'DS' ) or die( 'Error!' );

/**
 * Description of MainController
 *
 * @author sergo.beruashvili
 */
class XApiHelper
{
	private static $httpVersion = "HTTP/1.1";

	public function execute()
	{
		// get the full request URI
		/* @var $uri URI */
		$uri = clone(URI::getInstance());
		// Get the path
		//Remove basepath
		$Path = str_replace( 'index.php', '', substr_replace( $uri->getPath(), '', 0, strlen( URI::base( true ) ) ) );
		//Set the route
		$uri->setPath( trim( $Path, '/' ) );
		$GetData = $uri->getQuery( true );
		$Route = $uri->getPath();
		$Segments = explode( '/', $Route );
		$Class = 'API';
		$Method = 'Default';
		switch ( count( $Segments ) )
		{
			case 1:
				$Method = C::_( '0', $Segments );
				unset( $Segments[0] );
				break;

			default:
				$Class = C::_( '0', $Segments );
				$Method = C::_( '1', $Segments );
				unset( $Segments[0] );
				unset( $Segments[1] );
				break;
		}
		$Class = 'X' . ucfirst( $Class );
		if ( !$this->LoadClass( $Class ) )
		{
			XApiHelper::SetResponse( '400' );
		}
		$Vars = array_merge( array_values( $Segments ), $GetData );		
		if ( !$this->CallMethod( $Class, $Method, $Vars ) )
		{
			XApiHelper::SetResponse( '400' );
		}

	}

	private function LoadClass( $Class )
	{
		if ( class_exists( $Class ) )
		{
			return true;
		}
		else
		{
			return false;
		}

	}

	private function CallMethod( $Class, $Method, $Vars )
	{
		if ( class_exists( $Class ) )
		{
			$API = new $Class( );
		}
		else
		{
			echo 'Bad Request!';
			return false;
		}
		if ( method_exists( $API, $Method ) )
		{
			$API->setVars( $Vars );
			$API->setDefVars();
			$M = ucfirst( $Method );
			$API->{$M}();
			return true;
		}
		echo 'Bad Request!';
		return false;

	}

	public static function SetContentType( $ContentType = 'text/html' )
	{
		$RestResponse = XRestResponse::GetInstance();
		return $RestResponse->setContentType( $ContentType );

	}

	public static function SetResponse( $StatusCode = 200, $ContentType = null )
	{
		$RestResponse = XRestResponse::GetInstance();
		$RestResponse->setStatusCode( $StatusCode );
		if ( $ContentType )
		{
			$RestResponse->setContentType( $ContentType );
		}
		return $RestResponse->setHttpHeaders();

	}

	public static function GetPostBody()
	{
		return file_get_contents( 'php://input' );

	}

	public static function SetHeader( $Header )
	{
		$RestResponse = XRestResponse::GetInstance();
		$RestResponse->setHeader( $Header );

	}

	public static function setHttpHeaders( $contentType, $statusCode )
	{
		$statusMessage = self::getHttpStatusMessage( $statusCode );
		header( self::$httpVersion . " " . $statusCode . " " . $statusMessage );
		header( "Content-Type:" . $contentType );

	}

	public static function getHttpStatusMessage( $statusCode )
	{
		$httpStatus = array(
				100 => 'Continue',
				101 => 'Switching Protocols',
				200 => 'OK',
				201 => 'Created',
				202 => 'Accepted',
				203 => 'Non-Authoritative Information',
				204 => 'No Content',
				205 => 'Reset Content',
				206 => 'Partial Content',
				300 => 'Multiple Choices',
				301 => 'Moved Permanently',
				302 => 'Found',
				303 => 'See Other',
				304 => 'Not Modified',
				305 => 'Use Proxy',
				306 => '(Unused)',
				307 => 'Temporary Redirect',
				400 => 'Bad Request',
				401 => 'Unauthorized',
				402 => 'Payment Required',
				403 => 'Forbidden',
				404 => 'Not Found',
				405 => 'Method Not Allowed',
				406 => 'Not Acceptable',
				407 => 'Proxy Authentication Required',
				408 => 'Request Timeout',
				409 => 'Conflict',
				410 => 'Gone',
				411 => 'Length Required',
				412 => 'Precondition Failed',
				413 => 'Request Entity Too Large',
				414 => 'Request-URI Too Long',
				415 => 'Unsupported Media Type',
				416 => 'Requested Range Not Satisfiable',
				417 => 'Expectation Failed',
				500 => 'Internal Server Error',
				501 => 'Not Implemented',
				502 => 'Bad Gateway',
				503 => 'Service Unavailable',
				504 => 'Gateway Timeout',
				505 => 'HTTP Version Not Supported' );
		return ($httpStatus[$statusCode]) ? $httpStatus[$statusCode] : $httpStatus[500];

	}

}
