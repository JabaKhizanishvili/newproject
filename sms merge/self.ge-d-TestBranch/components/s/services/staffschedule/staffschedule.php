<?php

class staffschedule
{
	public function GetService()
	{
		$Schedule = (int) trim( Request::getVar( 'schedule' ) );
		$Data = Xstaffschedule::printData( $Schedule );
		$return = '';
		foreach ( $Data as $key => $value )
		{
			if ( $value )
			{
				$return .= '<div>';
				$return .= '<strong>' . Text::_( $key ) . ': </strong>';
				$return .= XTranslate::_( $value );
				$return .= '</div>';
			}
			else
			{
				$return .= '<div class="text-danger">';
				$return .= '<strong>' . Text::_( $key ) . ': </strong>';
				$return .= Text::_( 'not defined!' );
				$return .= '</div>';
			}
		}
		return $return;

	}

}
