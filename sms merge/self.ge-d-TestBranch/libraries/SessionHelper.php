<?php

class SessionHelper
{
	public static function getSessionHelper()
	{
		$Type = Users::GetUserData( 'SESSION_TYPE' );
		if ( $Type )
		{
			return true;
		}
		$ST = Helper::getConfig( 'standard_session_duration' );
		$StandardDuration = !empty( $ST ) ? ($ST * 60000) + 10000 : 30 * 60000;
		$base = URI::root();
		$on_off = Helper::getConfig( 'session_autologout' );
		$subtr = (int) Helper::getConfig( 'sessioncountdown' ) * 1000;
		$time = ($StandardDuration - $subtr);
		$keepAliveUrl = $base . '?option=profile&task=keepAlive';
		$logoutUrl = $base . '?option=logout';
		$redirectUrl = $base . '?option=logout';
		$title = Text::_( 'Your Session is About to Expire!' );
		$message = Text::_( 'Your Session is About to Expire!' );
		$logoutButton = Text::_( 'Logout' );
		$keepAliveButton = Text::_( 'Stay Connected' );
		$countdownMessage = Text::_( 'Redirecting in' );
		$seconds = Text::_( 'seconds.' );
		if ( Users::isLogged() )
		{
			if ( $on_off == 1 )
			{

				Helper::SetJS( '
								          $.sessionTimeout({
                                             keepAliveUrl: "' . $keepAliveUrl . '",
                                             title: "' . $title . '",
                                             message: "",
                                             logoutButton: "' . $logoutButton . '",
                                             keepAliveButton: "' . $keepAliveButton . '",
                                             logoutUrl: "' . $logoutUrl . '",
                                             redirUrl: "' . $redirectUrl . '",
                                             warnAfter: ' . ($time - $subtr) . ',
                                             redirAfter: ' . $time . ',
                                             countdownMessage: "' . $countdownMessage . ' {timer} ' . $seconds . '"
                                         });'
				);
			}
			else
			{
				Helper::SetJS( '
								          $.sessionTimeout({
                                             keepAliveUrl: "' . $base . '?option=profile&task=keepAlive",
                                             logoutUrl: "' . $base . '?option=logout",
                                             redirUrl: "' . $base . '/?option=logout",
                                             warnAfter: "' . ($time - $subtr) . '",
                                             onWarn: function() {} ,
                                             redirAfter: "' . $StandardDuration . '",
                                         });'
				);
			}
		}

	}

}
