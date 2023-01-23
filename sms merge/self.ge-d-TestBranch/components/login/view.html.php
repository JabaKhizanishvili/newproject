<?php

class LoginView extends View
{
	function display( $tpl = null )
	{
		$Option = Request::getCmd( 'option' );
		if ( !empty( $Option ) && $Option != 'login' )
		{
			$Uri = URI::getInstance();
			$Path = base64_encode( $Uri->toString( array( 'path', 'query', 'fragment' ) ) );
			$Url = '?return=' . urlencode( $Path );
			Users::Redirect( $Url );
		}
		parent::display( $tpl );

	}

}
