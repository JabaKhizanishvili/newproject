<?php

class XAPI
{
	protected $_Vars = array();

	public function getVars( $Key = null )
	{
		if ( !is_null( $Key ) )
		{
			return C::_( $Key, $this->_Vars );
		}
		return $this->_Vars;

	}

	public function setDefVars()
	{
		$Post = Request::get( 'post' );
		$this->_Vars['post'] = $Post;
		$Get = Request::get( 'get' );
		$this->_Vars['get'] = $Get;
		return true;

	}

	public function setVars( $Vars )
	{
		$this->_Vars = $Vars;
		return true;

	}

	public function _CheckAccess()
	{
		return;
		$IPx = array_flip( $this->_AllowedIPs );
		$IP = Request::getVar( 'REMOTE_ADDR', null, 'server' );
		if ( !isset( $IPx[$IP] ) )
		{
			XApiHelper::SetResponse( '403' );
			die;
		}

	}

	public function getVar( $Key, $DefVal = null )
	{
		return C::_( $Key, $this->_Vars, $DefVal );

	}

	/**
	 * 
	 * @param type $Response
	 */
	protected function _Print( $Response, $Code = 0 )
	{
		ignore_user_abort();
//		ob_start();
		echo json_encode( $Response, JSON_UNESCAPED_UNICODE );
		$Content = ob_get_clean();
		$Length = strlen( $Content );
		if ( $Code > 0 )
		{
			XApiHelper::SetResponse( $Code );
		}
		XApiHelper::SetHeader( 'Content-Encoding: none' );
		XApiHelper::SetHeader( 'Content-Length: ' . $Length );
		XApiHelper::SetHeader( 'Connection: Close' );
		echo $Content;
		@ob_flush();
		@flush();

	}

}
