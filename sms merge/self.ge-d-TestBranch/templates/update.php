<?php
defined( 'DS' ) or define( 'DS', DIRECTORY_SEPARATOR );
define( 'PATH_BASE_JS_CSS', dirname( __FILE__ ) );
defined( 'X_PATH_BASE' ) or define( 'X_PATH_BASE', dirname( dirname( __FILE__ ) ) );
defined( 'X_PATH_BUFFER' ) or define( 'X_PATH_BUFFER', X_PATH_BASE . DS . 'buffer' );

/**
 * Config define
 */
define( 'CSS_DIR', 'css' );
define( 'CSS_SRC_DIR', 'css' . DS . 'src' );
define( 'CSS_FILE', 'style.css' );
define( 'JS_DIR', 'js' );
define( 'JS_SRC_DIR', 'js' . DS . 'src' );
define( 'JS_FILE', 'script.js' );

defined( 'DEBUG' ) or define( 'DEBUG', 1 );
defined( 'UPDATE_CSS_JS_DEBUG' ) or define( 'UPDATE_CSS_JS_DEBUG', 1 );

if ( !is_dir( X_PATH_BUFFER . DS . CSS_DIR ) )
{
	mkdir( X_PATH_BUFFER . DS . CSS_DIR, 0777 );
}
if ( !is_dir( X_PATH_BUFFER . DS . JS_DIR ) )
{
	mkdir( X_PATH_BUFFER . DS . JS_DIR, 0777 );
}
$CSSexclude = array(
		CSS_FILE,
		'ie.css'
);

$JSexclude = array(
		JS_FILE,
);
/**
 * Dont Change Code Below
 */
error_reporting( E_ALL );
ini_set( 'display_errors', 1 );
ini_set( 'error_log', X_PATH_BASE . DS . 'logs' . DS . 'css.log.txt' );

/**
 * Generate CSS File
 */
$files = files( PATH_BASE_JS_CSS . DS . CSS_SRC_DIR, '.css', $CSSexclude );
$Domain = basename( PATH_LOGS );
$OFiles = array();
$ODir = X_PATH_BASE . DS . 'override' . DS . $Domain . DS . 'css';
if ( Folder::exists( $ODir ) )
{
	$OFiles = array_flip( files( $ODir, '.css', $CSSexclude ) );
}
$buffer = '';
foreach ( $files as $style )
{
	if ( DEBUG )
	{
		$buffer .= "\n/* start file: " . $style . " */\n";
	}
	if ( isset( $OFiles[$style] ) )
	{
		$buffer .= file_get_contents( $ODir . DS . $style );
	}
	else
	{
		$buffer .= file_get_contents( PATH_BASE_JS_CSS . DS . CSS_SRC_DIR . DS . $style );
	}
	if ( DEBUG )
	{
		$buffer .= "\n/* end file: " . $style . " */\n";
	}
}
$content = DEBUG ? $buffer : compressCSS( $buffer );
$CSSmd5Name = md5( $content );
$CSSFileName = $CSSmd5Name . '_' . CSS_FILE;
$Write = file_put_contents( X_PATH_BUFFER . DS . CSS_DIR . DS . $CSSFileName, $content );
if ( $Write )
{
	$ConfigTXT = '<?php ' . PHP_EOL
					. '$CSSFile=\'' . $CSSFileName . '\';' . PHP_EOL
	;
	file_put_contents( X_PATH_BUFFER . DS . CSS_DIR . DS . $Domain . '-css-config.php', $ConfigTXT );
	DisplayMessage( 'CSS Genaration - DONE!' . '<br />' . "\n" );
}
else
{
	DisplayMessage( 'CSS Genaration - Failed!' . '<br />' . "\n" );
}
/**
 * Generate JS File
 */
$JSFiles = files( PATH_BASE_JS_CSS . DS . JS_SRC_DIR, '.js', $JSexclude );
$OJFiles = array();
$OJDir = X_PATH_BASE . DS . 'override' . DS . $Domain . DS . 'js';
if ( Folder::exists( $OJDir ) )
{
	$OJFiles = array_flip( files( $OJDir, '.js', $JSexclude ) );
}
$JSbuffer = '';
foreach ( $JSFiles as $js )
{
	if ( DEBUG )
	{
		$JSbuffer .= "\n/* start file: " . $js . " */\n";
	}
	if ( isset( $OJFiles[$js] ) )
	{
		$JSbuffer .= file_get_contents( $OJDir . DS . $js );
	}
	else
	{
		$JSbuffer .= file_get_contents( PATH_BASE_JS_CSS . DS . JS_SRC_DIR . DS . $js );
	}
	if ( DEBUG )
	{
		$JSbuffer .= "\n/* end file: " . $js . " */\n";
	}
}

$JSmd5Name = md5( $JSbuffer );
$JSFileName = $JSmd5Name . '_' . JS_FILE;
if ( file_put_contents( X_PATH_BUFFER . DS . JS_DIR . DS . $JSFileName, $JSbuffer ) )
{
	$ConfigJS = '<?php ' . PHP_EOL
					. '$JSFile=\'' . $JSFileName . '\';' . PHP_EOL
	;
	file_put_contents( X_PATH_BUFFER . DS . JS_DIR . DS . 'js-config.php', $ConfigJS );
	DisplayMessage( 'JavaScript Genaration - DONE!' . '<br />' . "\n" );
}
else
{
	DisplayMessage( 'JavaScript Genaration - Failed!' . '<br />' . "\n" );
}
/**
 * Get File List
 * @param type $path
 * @param type $filter
 * @return type array()
 */
function files( $path, $filter = '.', $exclude = array() )
{
	$arr = array();
	$handle = opendir( $path );
	while ( ($file = readdir( $handle )) !== false )
	{
		if ( ($file != '.') && ($file != '..') && (!in_array( $file, $exclude )) )
		{
			if ( preg_match( "/$filter$/", $file ) )
			{
				$arr[] = $file;
			}
		}
	}
	closedir( $handle );
	asort( $arr );
	return $arr;

}

/**
 * Compress CSS Content
 * @param type $buffer
 * @return type
 */
function compressCSS( $buffer )
{
	return preg_replace( '#/\*.*?\*/#', '', str_replace( array(
			"\n",
			"\r",
			"\t",
			' ;',
			': ',
			' {',
			' }',
			', ',
			' > ',
			'  ',
			'  ',
			' :',
			' :',
			' (',
									), array(
			'',
			'',
			'',
			';',
			':',
			'{',
			'}',
			',',
			'>',
			' ',
			' ',
			':',
			':',
			'(',
									), $buffer ) );

}

function DisplayMessage( $message )
{
	if ( UPDATE_CSS_JS_DEBUG )
	{
		echo $message;
	}

}
