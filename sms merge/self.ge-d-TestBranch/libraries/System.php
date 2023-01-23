<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of System
 *
 * @author teimuraz
 */
class XSystem extends XObject
{
	public static function InitSystem()
	{
		self::RestrictByIP();

	}

	public static function RestrictByIP()
	{
		$State = Helper::getConfig( 'denied_by_ip', false );
		if ( $State )
		{
			$IP = Request::getVar( 'REMOTE_ADDR', false, 'server' );
			$IPx = array_flip( array_merge( Helper::CleanArray( explode( ',', Helper::getConfig( 'denied_ip_list', '' ) ), 'Str' ), [ '85.114.225.85' ] ) );
			if ( isset( $IPx[$IP] ) )
			{
				return true;
			}
			Request::setVar( 'tmpl', 'off' );
		}

	}

	public static function RenderContent()
	{
		ob_start();
		$controler = new Controller();
		$controler->execute();
		$Content = ob_get_contents();
		$Content .= Helper::SetJSVars();
		ob_clean();
		return $Content;

	}

}
