<?php

/**
 * Text  handling class
 *
 * @static
 * @package 	WSCMS.Framework
 * @subpackage	Language
 * @since		1.5
 */
class Text
{
	/**
	 * Translates a string into the current language
	 *
	 * @access	public
	 * @param	string $string The string to translate
	 * @param	boolean	$jsSafe		Make the result javascript safe
	 * @since	1.5
	 *
	 */
	public static function _( $string, $jsSafe = false )
	{
		if ( empty( $string ) )
		{
			return $string;
		}
		$lang = Language::getInstance( SYSTEM_LANG );
		return $lang->_( $string, $jsSafe );

	}

	/**
	 * Passes a string thru an sprintf
	 *
	 * @access	public
	 * @param	format The format string
	 * @param	mixed Mixed number of arguments for the sprintf function
	 * @since	1.5
	 */
	public static function sprintf()
	{
		$lang = Language::getInstance( SYSTEM_LANG );
		$args = func_get_args();
		if ( count( $args ) > 0 )
		{
			$args[0] = $lang->_( $args[0] );
			return call_user_func_array( 'sprintf', $args );
		}
		return '';

	}

	/**
	 * Passes a string thru an printf
	 *
	 * @access	public
	 * @param	format The format string
	 * @param	mixed Mixed number of arguments for the sprintf function
	 * @since	1.5
	 */
	public static function printf()
	{
		$lang = Language::getInstance( SYSTEM_LANG );
		$args = func_get_args();
		if ( count( $args ) > 0 )
		{
			$args[0] = $lang->_( $args[0] );
			return call_user_func_array( 'printf', $args );
		}
		return '';

	}

	protected function _load( $filename, $extension = 'unknown', $overwrite = true )
	{
		$result = false;
		if ( $content = @file_get_contents( $filename ) )
		{
			//Take off BOM if present in the ini file
			if ( $content[0] == "\xEF" && $content[1] == "\xBB" && $content[2] == "\xBF" )
			{
				$content = substr( $content, 3 );
			}
			$registry = new Registry();
			$registry->loadINI( $content );
			$newStrings = $registry->toArray();

			if ( is_array( $newStrings ) )
			{
				$this->_strings = $overwrite ? array_merge( $this->_strings, $newStrings ) : array_merge( $newStrings, $this->_strings );
				$result = true;
			}
		}
		$this->_logTimes[$filename] = microtime( true ) - $logTime;

		// Record the result of loading the extension's file.
		if ( !isset( $this->_paths[$extension] ) )
		{
			$this->_paths[$extension] = array();
		}

		$this->_paths[$extension][$filename] = $result;

		return $result;

	}

}
