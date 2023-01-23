<?php
(PHP_SAPI !== 'cli' || isset( $_SERVER['HTTP_USER_AGENT'] )) && die( 'CLI Only' );
error_reporting( E_ALL );
ini_set( 'display_errors', 1 );
define( 'DS', DIRECTORY_SEPARATOR );
$BaseLib = dirname( __FILE__ ); //str_replace( DS . basename( dirname( __FILE__ ) ), '', dirname( __FILE__ ) );
define( 'PATH_BASE', $BaseLib );

class XMap
{
	public static function GenerateXMap( $Base, $Path )
	{
		$Files = self::Files( $Base, $Path );
		$Classes = array();
		foreach ( $Files as $File )
		{
			$Class = self::ParseClassFile( $Base . $File );
			$Path = ltrim( str_replace( DS, '.', self::stripExt( $File ) ), '.' );
			foreach ( $Class as $ClassItem )
			{
				$ClassName = mb_strtolower( $ClassItem );
				if ( isset( $Classes[$ClassName] ) )
				{
					echo $ClassName . ' - Exists!' . ' File : ' . $File . PHP_EOL;
				}
				else
				{
					$Classes[$ClassName] = $Path;
				}
			}
		}
		return self::WriteMapFile( $Classes, $Base );

	}

	public static function Files( $Base, $Current, $Filter = '\.php$' )
	{
		$FilesList = array();
		$Handle = opendir( $Current );
		while ( ($item = readdir( $Handle )) !== false )
		{
			if ( ($item != '.') && ($item != '..') )
			{
				$FullItem = $Current . DS . $item;
				$isDir = is_dir( $FullItem );
				if ( $isDir )
				{
					$TMPList = XMap::Files( $Base, $FullItem, $Filter );
					$FilesList = array_merge( $FilesList, $TMPList );
				}
				else
				{
					if ( preg_match( "/$Filter/", $item ) )
					{
						$FilesList[] = substr( $FullItem, strlen( $Base ) );
					}
				}
			}
		}
		closedir( $Handle );
		asort( $FilesList );
		return $FilesList;

	}

	public static function stripExt( $File )
	{
		return preg_replace( '#\.[^.]*$#', '', $File );

	}

	public static function ParseClassFile( $File )
	{
		$Code = file_get_contents( $File );
		$Classes = array();
		$tokens = token_get_all( $Code );
		$Count = count( $tokens );
		for ( $i = 2; $i < $Count; $i++ )
		{
			if ( $tokens[$i - 2][0] == T_CLASS && $tokens[$i - 1][0] == T_WHITESPACE && $tokens[$i][0] == T_STRING )
			{
				$Classes[] = $tokens[$i][1];
			}
			else if ( $tokens[$i - 2][0] == T_INTERFACE && $tokens[$i - 1][0] == T_WHITESPACE && $tokens[$i][0] == T_STRING )
			{
				$Classes[] = $tokens[$i][1];
			}
		}
		return $Classes;

	}

	public static function WriteMapFile( $Classes, $Base )
	{
		$Data = '<?php' . PHP_EOL
						. 'defined( \'DS\') or die(\'Access Denied!\');' . PHP_EOL
						. '$XMapData = array(' . PHP_EOL;
		foreach ( $Classes as $Key => $Value )
		{
			$Key . ' => ' . $Value . PHP_EOL;
			$Data .= '\'' . $Key . '\'=>' . '\'' . $Value . '\',' . PHP_EOL;
		}
		$Data .= ');';
		return file_put_contents( $Base . DS . 'Class.Map.php', $Data );

	}

}

XMap::GenerateXMap( PATH_BASE, PATH_BASE );
