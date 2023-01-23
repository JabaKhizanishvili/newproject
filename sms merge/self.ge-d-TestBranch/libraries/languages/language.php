<?php

/**
 * Languages/translation handler class
 *
 * @package 	WSCMS.Framework
 * @subpackage	Language
 * @since		1.5
 */
class Language
{
	/**
	 * Debug language, If true, highlights if string isn't found
	 *
	 * @var		boolean
	 * @access	protected
	 * @since	1.5
	 */
	protected $_debug = false;

	/**
	 * The default language
	 *
	 * The default language is used when a language file in the requested language does not exist.
	 *
	 * @var		string
	 * @access	protected
	 * @since	1.5
	 */
	protected $_default = 'ka-ge';

	/**
	 * An array of orphaned text
	 *
	 * @var		array
	 * @access	protected
	 * @since	1.5
	 */
	protected $_orphans = array();

	/**
	 * Array holding the language metadata
	 *
	 * @var		array
	 * @access	protected
	 * @since	1.5
	 */
	protected $_metadata = null;

	/**
	 * The language to load
	 *
	 * @var		string
	 * @access	protected
	 * @since	1.5
	 */
	protected $_lang = null;

	/**
	 * List of language files that have been loaded
	 *
	 * @var		array of arrays
	 * @access	public
	 * @since	1.5
	 */
	protected $_paths = array();

	/**
	 * Translations
	 *
	 * @var		array
	 * @access	protected
	 * @since	1.5
	 */
	protected $_strings = null;

	/**
	 * An array of used text, used during debugging
	 *
	 * @var		array
	 * @access	protected
	 * @since	1.5
	 */
	protected $_used = array();

	/**
	 * An array of parse times, used during debugging
	 *
	 * @var		array
	 * @access	protected
	 * @since	1.5
	 */
	protected $_logTimes = array();
	public $untranslated = array();

	/**
	 * Constructor activating the default information of the language
	 *
	 * @access	protected
	 */
	public function __construct( $lang = null )
	{
		$this->_strings = array();

		if ( $lang == null )
		{
			$lang = $this->_default;
		}

		$this->_default = $lang;
		$this->_lang = $lang;

		$this->load();

	}

	/**
	 * Returns a reference to a language object
	 *
	 * This method must be invoked as:
	 * 		<pre>  $browser = &Language::getInstance([$lang);</pre>
	 *
	 * @access	public
	 * @param	string $lang  The language to use.
	 * @return	Language  The Language object.
	 * @since	1.5
	 */
	public static function getInstance( $lang )
	{
		static $instance = NULL;
		if ( $instance )
		{
			return $instance;
		}
		else
		{
			$instance = new Language( $lang );
		}
		return $instance;

	}

	/**
	 * Translate function, mimics the php gettext (alias _) function
	 *
	 * @access	public
	 * @param	string		$string 	The string to translate
	 * @param	boolean	$jsSafe		Make the result javascript safe
	 * @return	string	The translation of the string
	 * @since	1.5
	 */
	public function _( $string, $jsSafe = false )
	{
		//$key = str_replace( ' ', '_', strtoupper( trim( $string ) ) );echo '<br />'.$key;

		$strings = strtoupper( $string );
		$key = substr( $strings, 0, 1 ) == '_' ? substr( $strings, 1 ) : $strings;

		if ( isset( $this->_strings[$key] ) )
		{
			$string = XTranslate::_( trim( $this->_strings[$key] ), 'langfile', 'ka' );
		}
		else
		{
			$this->untranslated[strtoupper( $string )] = strtoupper( $string ) . '=' . $string;
			if ( defined( $string ) )
			{
				$string = constant( $string );
			}
//			\Sentry\configureScope( function ( \Sentry\State\Scope $scope ): void
//			{
//				$scope->setTag( 'page', 'LangText' );
//			} );
//			\Sentry\captureMessage( strtoupper( $string ) . '=' . $string );
		}

		if ( $jsSafe )
		{
			$string = addslashes( $string );
		}
		return $string;

	}

	/**
	 * Loads a single language file and appends the results to the existing strings
	 *
	 * @access	public
	 * @param	string 	$extension 	The extension for which a language file should be loaded
	 * @param	string 	$basePath  	The basepath to use
	 * @param	string	$lang		The language to load, default null for the current language
	 * @param	boolean $reload		Flag that will force a language to be reloaded if set to true
	 * @return	boolean	True, if the file has successfully loaded.
	 * @since	1.5
	 */
	public function load( $basePath = BASE_PATH, $lang = null, $reload = false )
	{
		if ( !$lang )
		{
			$lang = $this->_default;
		}
		$filename = Language::getLanguagePath( $basePath, $lang );

		$result = false;
		if ( is_array( $filename ) )
		{
			foreach ( $filename as $File )
			{
				if ( isset( $this->_paths[$File] ) && !$reload )
				{
					// Strings for this file have already been loaded
					$result = true;
				}
				else
				{
					// Load the language file
					$result = $this->_load( $File );
				}
			}
		}
		else
		{

			if ( isset( $this->_paths[$filename] ) && !$reload )
			{
				// Strings for this file have already been loaded
				$result = true;
			}
			else
			{
				// Load the language file
				$result = $this->_load( $filename );

				// Check if there was a problem with loading the file
				if ( $result === false )
				{
					die( 'Language File Not Defined!' );
				}
			}
			return $result;
		}

	}

	/**
	 * Loads a language file
	 *
	 * This method will not note the successful loading of a file - use load() instead
	 *
	 * @access	private
	 * @param	string The name of the file
	 * @param	string The name of the extension
	 * @return	boolean True if new strings have been added to the language
	 * @see		Language::load()
	 * @since	1.5
	 */
	protected function _load( $filename, $overwrite = true )
	{
		$result = false;
		$content = @file_get_contents( $filename );
		if ( $content )
		{
			//Take off BOM if present in the ini file
			if ( $content[0] == "\xEF" && $content[1] == "\xBB" && $content[2] == "\xBF" )
			{
				$content = substr( $content, 3 );
			}

			$newStrings = $this->_stringToArray( $content );

			if ( is_array( $newStrings ) )
			{
				$this->_strings = $overwrite ? array_merge( $this->_strings, $newStrings ) : array_merge( $newStrings, $this->_strings );
				$result = true;
			}
		}
		$this->_paths[$filename] = $result;
		return $result;

	}

	/**
	 * Get the path to a language
	 *
	 * @access	public
	 * @param	string $basePath  The basepath to use
	 * @param	string $language	The language tag
	 * @return	string	language related path or null
	 * @since	1.5
	 */
	public static function getLanguagePath( $basePath = BASE_PATH, $language = null )
	{
		$dir = $basePath . DS . 'language';
		$FIles = Folder::files( $dir, $language . '([0-9a-zA-Z\.]*)\.ini', false, true );
		return $FIles;

	}

	/**
	 * Parse an .ini string, based on phpDocumentor phpDocumentor_parse_ini_file function
	 *
	 * @access public
	 * @param mixed The INI string or array of lines
	 * @param boolean add an associative index for each section [in brackets]
	 * @return object Data Object
	 */
	private function _stringToArray( $data, $process_sections = null )
	{
		static $inistocache = null;

		if ( !isset( $inistocache ) )
		{
			$inistocache = array();
		}

		if ( is_string( $data ) )
		{
			$lines = explode( "\n", $data );
			$hash = md5( $data );
		}
		else
		{
			if ( is_array( $data ) )
			{
				$lines = $data;
			}
			else
			{
				$lines = array();
			}
			$hash = md5( implode( "\n", $lines ) );
		}

		if ( array_key_exists( $hash, $inistocache ) )
		{
			return $inistocache[$hash];
		}

		$obj = new stdClass();

		$sec_name = '';
		$unparsed = 0;
		if ( !$lines )
		{
			return $obj;
		}

		foreach ( $lines as $line )
		{
			// ignore comments
			if ( $line && $line[0] == ';' )
			{
				continue;
			}

			$line = trim( $line );

			if ( $line == '' )
			{
				continue;
			}

			$lineLen = strlen( $line );
			if ( $line && $line[0] == '[' && $line[$lineLen - 1] == ']' )
			{
				$sec_name = substr( $line, 1, $lineLen - 2 );
				if ( $process_sections )
				{
					$obj->{$sec_name} = new stdClass();
				}
			}
			else
			{
				$pos = strpos( $line, '=' );
				if ( $pos )
				{
					$property = trim( substr( $line, 0, $pos ) );

					// property is assumed to be ascii
					if ( $property && $property[0] == '"' )
					{
						$propLen = strlen( $property );
						if ( $property[$propLen - 1] == '"' )
						{
							$property = stripcslashes( substr( $property, 1, $propLen - 2 ) );
						}
					}
					// AJE: 2006-11-06 Fixes problem where you want leading spaces
					// for some parameters, eg, class suffix
					// $value = trim(substr($line, $pos +1));
					$value = substr( $line, $pos + 1 );

					if ( strpos( $value, '|' ) !== false && preg_match( '#(?<!\\\)\|#', $value ) )
					{
						$newlines = explode( '\n', $value );
						$values = array();
						foreach ( $newlines as $newlinekey => $newline )
						{

							// Explode the value if it is serialized as an arry of value1|value2|value3
							$parts = preg_split( '/(?<!\\\)\|/', $newline );
							$array = (strcmp( $parts[0], $newline ) === 0) ? false : true;
							$parts = str_replace( '\|', '|', $parts );

							foreach ( $parts as $key => $value )
							{
								if ( $value == 'false' )
								{
									$value = false;
								}
								else if ( $value == 'true' )
								{
									$value = true;
								}
								else if ( $value && $value[0] == '"' )
								{
									$valueLen = strlen( $value );
									if ( $value[$valueLen - 1] == '"' )
									{
										$value = stripcslashes( substr( $value, 1, $valueLen - 2 ) );
									}
								}
								if ( !isset( $values[$newlinekey] ) )
								{
									$values[$newlinekey] = array();
								}
								$values[$newlinekey][] = str_replace( '\n', "\n", $value );
							}

							if ( !$array )
							{
								$values[$newlinekey] = $values[$newlinekey][0];
							}
						}

						if ( $process_sections )
						{
							if ( $sec_name != '' )
							{
								$obj->{$sec_name}->{$property} = $values[$newlinekey];
							}
							else
							{
								$obj->{$property} = $values[$newlinekey];
							}
						}
						else
						{
							$obj->{$property} = $values[$newlinekey];
						}
					}
					else
					{
						//unescape the \|
						$value = str_replace( '\|', '|', $value );

						if ( $value == 'false' )
						{
							$value = false;
						}
						else if ( $value == 'true' )
						{
							$value = true;
						}
						else if ( $value && $value[0] == '"' )
						{
							$valueLen = strlen( $value );
							if ( $value[$valueLen - 1] == '"' )
							{
								$value = stripcslashes( substr( $value, 1, $valueLen - 2 ) );
							}
						}

						if ( $process_sections )
						{
							$value = str_replace( '\n', "\n", $value );
							if ( $sec_name != '' )
							{
								$obj->{$sec_name}->{$property} = $value;
							}
							else
							{
								$obj->{$property} = $value;
							}
						}
						else
						{
							$obj->{$property} = str_replace( '\n', "\n", $value );
						}
					}
				}
				else
				{
					if ( $line && $line[0] == ';' )
					{
						continue;
					}
					if ( $process_sections )
					{
						$property = '__invalid' . $unparsed++ . '__';
						if ( $process_sections )
						{
							if ( $sec_name != '' )
							{
								$obj->{$sec_name}->{$property} = trim( $line );
							}
							else
							{
								$obj->{$property} = trim( $line );
							}
						}
						else
						{
							$obj->{$property} = trim( $line );
						}
					}
				}
			}
		}

		$inistocache[$hash] = clone($obj);

		return (array) $obj;

	}

	public function GetStrings()
	{
		return $this->_strings;

	}

}
