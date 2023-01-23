<?php
/**
 * @version		$Id: html.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Framework
 * @copyright	Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
defined( 'PATH_BASE' ) or die();

/**
 * Utility class for all HTML drawing classes
 *
 * @static
 * @package 	WSCMS.Framework
 * @subpackage	HTML
 * @since		1.5
 */
abstract class HTML
{
	/**
	 * Class loader method
	 *
	 * Additional arguments may be supplied and are passed to the sub-class.
	 * Additional include paths are also able to be specified for third-party use
	 *
	 * @param	string	The name of helper method to load, (prefix).(class).function
	 *                  prefix and class are optional and can be used to load custom
	 *                  html helpers.
	 */
	public static function _( $type )
	{
//Initialise variables
		$prefix = 'HTML';
		$file = '';
		$func = $type;

// Check to see if we need to load a helper file
		$parts = explode( '.', $type );

		switch ( count( $parts ) )
		{
			case 3 :
				$prefix = preg_replace( '#[^A-Z0-9_]#i', '', $parts[0] );
				$file = preg_replace( '#[^A-Z0-9_]#i', '', $parts[1] );
				$func = preg_replace( '#[^A-Z0-9_]#i', '', $parts[2] );
				break;

			case 2 :
				$file = preg_replace( '#[^A-Z0-9_]#i', '', $parts[0] );
				$func = preg_replace( '#[^A-Z0-9_]#i', '', $parts[1] );
				break;
		}
		$className = $prefix . ucfirst( $file );
		if ( !class_exists( $className ) )
		{
			$ClassFile = dirname( __FILE__ ) . DS . 'parameter' . DS . 'element' . DS . mb_strtolower( $file ) . '.php';
			if ( is_file( $ClassFile ) )
			{
				require_once $ClassFile;

				if ( !class_exists( $className ) )
				{
					echo $className . '::' . $func . ' not found in file.';
					return false;
				}
			}
			else
			{
				echo $prefix . $file . ' not supported. File not found.';
				return false;
			}
		}

		if ( is_callable( array( $className, $func ) ) )
		{
			$temp = func_get_args();
			array_shift( $temp );
			$args = array();
			foreach ( $temp as $k => $v )
			{
				$args[] = &$temp[$k];
			}
			return call_user_func_array( array( $className, $func ), $args );
		}
		else
		{
			XError::raiseWarning( 0, $className . '::' . $func . ' not supported.' );
			return false;
		}

	}

	/**
	 * HTML Form Carcass
	 * 
	 * @access	public
	 * @static
	 * @param String $Label Fields Label Text
	 * @param String $Element Form Element HTML Code
	 * @param String $description Form Element Description
	 * @param String $id Form Element ID
	 */
	public static function __( $Label = '', $Element = '', $description = '', $id = '', $must = false, $hidden = false )
	{
		$ForID = '';
		ob_start();
		if ( $id )
		{
			$ForID = 'for="' . $id . '"';
		}
		if ( $hidden )
		{
			echo $Element;
		}
		else
		{
			?>
			<div class="form_field">
				<div class="form_item">
					<div class="form_label">
						<label class="form_param_lbl" <?php echo $ForID; ?>>
							<?php echo Text::_( $Label ); ?>
						</label>
					</div>
					<div class="form_input_area">
						<?php
						echo $Element;
						?>
					</div>
					<div class="cls"></div>
				</div>
				<?php
				if ( $description )
				{
					?>
					<div class="form_desc">
						<span class="form_param_desc">
							<?php echo Text::_( $description ); ?>
						</span>
					</div>
					<?php
				}
				?>
				<div class="cls"></div>
			</div>
			<?php
		}
		$content = ob_get_clean();
		return $content;

	}

	/**
	 * Write a <a></a> element
	 *
	 * @access	public
	 * @param	string 	The relative URL to use for the href attribute
	 * @param	string	The target attribute to use
	 * @param	array	An associative array of attributes to add
	 * @since	1.5
	 */
	public static function link( $url, $text, $attribs = null )
	{
		if ( is_array( $attribs ) )
		{
			$attribs = ArrayHelper::toString( $attribs );
		}

		return '<a href="' . $url . '" ' . $attribs . '>' . $text . '</a>';

	}

	/**
	 * Write a <img></amg> element
	 *
	 * @access	public
	 * @param	string 	The relative or absoluete URL to use for the src attribute
	 * @param	string	The target attribute to use
	 * @param	array	An associative array of attributes to add
	 * @since	1.5
	 */
	public static function image( $url, $alt, $attribs = null )
	{
		if ( is_array( $attribs ) )
		{
			$attribs = ArrayHelper::toString( $attribs );
		}

		if ( strpos( $url, 'http' ) !== 0 )
		{
			$url = URI::root( true ) . '/' . $url;
		}

		return '<img src="' . $url . '" alt="' . Text::_( $alt ) . '" title="' . Text::_( $alt ) . '" ' . $attribs . ' />';

	}

	/**
	 * Write a Status element
	 *
	 * @access	public
	 * @param	string 	The relative or absoluete URL to use for the src attribute
	 * @param	string	The target attribute to use
	 * @param	array	An associative array of attributes to add
	 * @since	1.5
	 */
	public static function Status( $status, $id = false, $option = DEFAULT_COMPONENT, $task = ' ' )
	{
		$status = intval( $status );
		$image = '';
		if ( $status == 1 )
		{
			$image = HTML::image( X_TEMPLATE . '/images/publish_g.png', 'Published' );
		}
		else
		{
			$image = HTML::image( X_TEMPLATE . '/images/publish_r.png', 'Not Published' );
		}
		$html = '';
		if ( $id !== false )
		{
			$html .= '<a href="javascript:doStateAction(\'' . $option . '\', \'' . $task . '\', ' . $id . ')" >';
			$html .= $image;
			$html .= '</a>';
		}
		else
		{
			$html = $image;
		}
		return $html;

	}

	/**
	 * Write a <iframe></iframe> element
	 *
	 * @access	public
	 * @param	string 	The relative URL to use for the src attribute
	 * @param	string	The target attribute to use
	 * @param	array	An associative array of attributes to add
	 * @param	string	The message to display if the iframe tag is not supported
	 * @since	1.5
	 */
	public static function iframe( $url, $name, $attribs = null, $noFrames = '' )
	{
		if ( is_array( $attribs ) )
		{
			$attribs = ArrayHelper::toString( $attribs );
		}

		return '<iframe src="' . $url . '" ' . $attribs . ' name="' . $name . '">' . $noFrames . '</iframe>';

	}

	/**
	 * Returns formated date according to a given format and time zone.
	 *
	 * @param	string	String in a format accepted by date(), defaults to "now".
	 * @param	string	format optional format for strftime
	 * @param	mixed	Time zone to be used for the date.  Special cases: boolean true for user
	 * 					setting, boolean false for server setting.
	 * @return	string	A date translated by the given format and time zone.
	 * @see		strftime
	 * @since	1.5
	 */
	public static function date( $input = 'now', $format = null, $tz = true )
	{
		if ( $input == Text::_( 'Never' ) )
		{
			return Text::_( 'Never' );
		}
		// Get some system objects.
		$config = Factory::getConfig();
		$user = Factory::getUser();
		// UTC date converted to user time zone.
		if ( $tz === true )
		{
			// Get a date object based on UTC.
			$date = Factory::getDate( $input, 'UTC' );

			// Set the correct time zone based on the user configuration.
			$date->setOffset( $user->getParam( 'timezone', $config->getValue( 'config.offset' ) ) );
		}
		// UTC date converted to server time zone.
		elseif ( $tz === false )
		{
			// Get a date object based on UTC.
			$date = Factory::getDate( $input, 'UTC' );

			// Set the correct time zone based on the server configuration.
			$date->setOffset( $config->getValue( 'config.offset' ) );
		}
		// No date conversion.
		elseif ( $tz === null )
		{
			$date = Factory::getDate( $input );
		}
		// UTC date converted to given time zone.
		else
		{
			// Get a date object based on UTC.
			$date = Factory::getDate( $input, 'UTC' );

			// Set the correct time zone based on the server configuration.
			$date->setOffset( $tz );
		}

		// If no format is given use the default locale based format.
		if ( !$format )
		{
			$format = Text::_( 'DATE_FORMAT_LC1' );
		}

		return $date->toFormat( $format, true );

	}

	/**
	 * Creates a tooltip with an image as button
	 *
	 * @access	public
	 * @param	string	$tooltip The tip string
	 * @param	string	$title The title of the tooltip
	 * @param	string	$image The image for the tip, if no text is provided
	 * @param	string	$text The text for the tip
	 * @param	string	$href An URL that will be used to create the link
	 * @param	boolean depreciated
	 * @return	string
	 * @since	1.5
	 */
	public static function tooltip( $tooltip, $title = '', $image = 'tooltip.png', $text = '', $href = '', $link = 1 )
	{
		$tooltip = addslashes( htmlspecialchars( $tooltip, ENT_QUOTES, 'UTF-8' ) );
		$title = addslashes( htmlspecialchars( $title, ENT_QUOTES, 'UTF-8' ) );

		if ( !$text )
		{
			$image = URI::root( true ) . '/includes/' . X_TEMPLATE . '/js/ThemeOffice/' . $image;
			$text = '<img src="' . $image . '" border="0" alt="' . Text::_( 'Tooltip' ) . '"/>';
		}
		else
		{
			$text = Text::_( $text, true );
		}

		if ( $title )
		{
			$title = $title . '::';
		}

		$style = 'style="text-decoration: none;
color: #333;"';

		if ( $href )
		{
			$href = Route::_( $href );
			$style = '';
			$tip = '<span class="editlinktip hasTip" title="' . $title . $tooltip . '" ' . $style . '><a href="' . $href . '">' . $text . '</a></span>';
		}
		else
		{
			$tip = '<span class="editlinktip hasTip" title="' . $title . $tooltip . '" ' . $style . '>' . $text . '</span>';
		}

		return $tip;

	}

	/**
	 * Displays a calendar control field
	 *
	 * @param	string	The date value
	 * @param	string	The name of the text field
	 * @param	string	The id of the text field
	 * @param	string	The date format
	 * @param	array	Additional html attributes
	 */
	public static function calendar( $value, $name, $id, $formatIN = '%Y-%m-%d', $attribs = null )
	{
		$format = str_replace( '%', '', $formatIN );
		if ( is_array( $attribs ) )
		{
			$attribs = ArrayHelper::toString( $attribs );
		}
//		Helper::SetJS( ''
////						. 'BindCalendar("' . $id . '"); '
//		);
		if ( $value )
		{
			$Date = new PDate( $value );
			$value = $Date->toFormat( '%d-%m-%Y' );
		}

		return '<div class="bfh-datepicker calendar_input_style" data-mode="24h" data-date="' . $value . '" id="' . $id . '" data-name="' . $name . '"  data-format="d-m-y">'
						. '</div>'
		;

	}

	/**
	 * Convert parameters in string
	 *
	 * @param array/object $params Parameters.
	 * @param string $view View name.
	 *
	 * @return string
	 */
	public static function convertParams( $params )
	{
		if ( !is_array( $params ) && !is_object( $params ) )
		{
			return false;
		}
		$string = '';

		foreach ( $params as $key => $value )
		{
			if ( ctype_digit( $key ) || is_object( $value ) || is_array( $value ) )
			{
				continue;
			}
			if ( is_object( $value ) || is_array( $value ) )
			{
				$value = implode( '|', (array) $value );
			}
			$string .= $key . "=" . str_replace( array( "\n" ), "<br />", $value ) . "\n";
		}
		return $string;

	}

	/**
	 * Render html with parameters and xml file
	 *
	 * @param string $params Parameters.
	 * @param string $file xml file name.
	 * @param string $name html names prefix.
	 * @param string $group xml file group name.
	 *
	 * @return string html data
	 */
	public static function renderParams( $params, $file, $name = 'params', $group = '_default' )
	{
		require_once 'parameter.php';
		$param = new Parameter( $params, $file );
		return $param->render( $name, $group );

	}

	/**
	 * Render html with parameters and xml file
	 *
	 * @param string $params Parameters.
	 * @param string $file xml file name.
	 * @param string $name html names prefix.
	 * @param string $group xml file group name.
	 *
	 * @return string html data
	 */
	public static function renderFilters( $params, $file, $config, $group = '_default' )
	{
		require_once 'filters.php';
		$param = new Filters( $params, $file, $config );
		return $param->render( $group );

	}

	/**
	 * Render html Page with parameters and xml file
	 *
	 * @param string $params Parameters.
	 * @param string $file xml file name.
	 * @param string $name html names prefix.
	 * @param string $group xml file group name.
	 *
	 * @return string html data
	 */
	public static function renderPage( $params, $file, $config, $group = '_default' )
	{
		require_once 'pages.php';
		$param = new Page( $params, $file, $config );
		return $param->render( $group );

	}

	public static function renderHistPage( $params, $allData = array(), $key = 0, $file = '', $config = array(), $group = '_default' )
	{
		require_once 'histpages.php';
		$param = new HistPage( $params, $allData, $key, $file, $config );
		return $param->render( $group );

	}

	/**
	 * 
	 * @param type $params
	 * @param type $file
	 * @param type $config
	 * @param type $group
	 * @return type
	 */
	public static function renderExport( $params, $file, $group = '_default' )
	{
		require_once 'export.php';
		$param = new Export( $params, $file );
		$Data = $param->render( $group );
		return $Data;

	}

	/**
	 * Render html Grid with parameters and xml file
	 *
	 * @param string $params Parameters.
	 * @param string $file xml file name.
	 * @param string $name html names prefix.
	 * @param string $group xml file group name.
	 *
	 * @return string html data
	 */
	public static function renderGrid( $params, $file, $config, $group = '_default' )
	{
		$param = new Grid( $params, $file, $config );
		return $param->render( $group );

	}

	/**
	 * Add a directory where HTML should search for helpers. You may
	 * either pass a string or an array of directories.
	 *
	 * @access	public
	 * @param	string	A path to search.
	 * @return	array	An array with directory elements
	 * @since	1.5
	 */
	public static function addIncludePath( $path = '' )
	{
		static $paths;

		if ( !isset( $paths ) )
		{
			$paths = array( PATH_LIBRARIES . DS . 'joomla' . DS . 'html' . DS . 'html' );
		}

// force path to array
		settype( $path, 'array' );

// loop through the path directories
		foreach ( $path as $dir )
		{
			if ( !empty( $dir ) && !in_array( $dir, $paths ) )
			{
				array_unshift( $paths, Path::clean( $dir ) );
			}
		}

		return $paths;

	}

	/**
	 * Displays a calendar control field
	 *
	 * @param	string	The date value
	 * @param	string	The name of the text field
	 * @param	string	The id of the text field
	 * @param	string	The date format
	 * @param	array	Additional html attributes
	 */
	public static function time( $value, $name, $id, $format = '%H:%M', $attribs = null )
	{
		$format = str_replace( '%', '', $format );
		if ( is_array( $attribs ) )
		{
			$attribs = ArrayHelper::toString( $attribs );
		}
//		Helper::SetJS( ''
//						. 'BindTime("' . $id . '"); '
//		);

		return '<div class="bfh-timepicker" data-mode="24h" data-time="' . $value . '"  id="' . $id . '" data-name="' . $name . '" >'
						. '</div>'
						. ''
//						. '<input type="text" name="' . $name . '" id="' . $id . '" value="' . htmlspecialchars( $value, ENT_COMPAT, 'UTF-8' ) . '" ' . $attribs . ' />'
						. '';

	}

}
