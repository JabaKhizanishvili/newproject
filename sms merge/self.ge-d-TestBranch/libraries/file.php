<?php

class XFile
{
	/**
	 * Gets the extension of a file name
	 *
	 * @param string $file The file name
	 * @return string The file extension
	 * @since 1.5
	 */
	public static function getExt( $file )
	{
		$chunks = explode( '.', $file );
		$chunksCount = count( $chunks ) - 1;

		if ( $chunksCount > 0 )
		{
			return $chunks[$chunksCount];
		}

		return false;

	}

	/**
	 * Strips the last extension off a file name
	 *
	 * @param string $file The file name
	 * @return string The file name without the extension
	 * @since 1.5
	 */
	public static function stripExt( $file )
	{
		return preg_replace( '#\.[^.]*$#', '', $file );

	}

	/**
	 * Makes file name safe to use
	 *
	 * @param string $file The name of the file [not full path]
	 * @return string The sanitised string
	 * @since 1.5
	 */
	public static function makeSafe( $file )
	{
		$regex = array( '#(\.){2,}#', '#[^A-Za-z0-9\.\_\- ]#', '#^\.#' );
		return preg_replace( $regex, '', $file );

	}

	/**
	 * Wrapper for the standard file_exists function
	 *
	 * @param string $file File path
	 * @return boolean True if path is a file
	 * @since 1.5
	 */
	public static function exists( $file )
	{
		return is_file( self::clean( $file ) );

	}

	/**
	 * Function to strip additional / or \ in a path name
	 *
	 * @static
	 * @param	string	$path	The path to clean
	 * @param	string	$ds		Directory separator (optional)
	 * @return	string	The cleaned path
	 * @since	1.5
	 */
	public static function clean( $path, $ds = DS )
	{
		$path = trim( $path );

		if ( empty( $path ) )
		{
			$path = BASE_PATH;
		}
		else
		{
			// Remove double slashes and backslahses and convert all slashes and backslashes to DS
			$path = preg_replace( '#[/\\\\]+#', $ds, $path );
		}

		return $path;

	}

	/**
	 * Returns the name, sans any path
	 *
	 * param string $file File path
	 * @return string filename
	 * @since 1.5
	 */
	public static function getName( $file )
	{
		$slash = strrpos( $file, DS );
		if ( $slash !== false )
		{
			return substr( $file, $slash + 1 );
		}
		else
		{
			return $file;
		}

	}

	/**
	 * Utility function to read the files in a folder.
	 *
	 * @param	string	The path of the folder to read.
	 * @param	string	A filter for file names.
	 * @param	mixed	True to recursively search into sub-folders, or an
	 * integer to specify the maximum depth.
	 * @param	boolean	True to return the full path to the file.
	 * @param	array	Array with names of files which should not be shown in
	 * the result.
	 * @return	array	Files in the given folder.
	 * @since 1.5
	 */
	public static function files( $path, $filter = '.', $recurse = false, $fullpath = false, $exclude = array( '.svn', 'CVS' ) )
	{
		// Initialize variables
		$arr = array();

		// Check to make sure the path valid and clean
		$path = self::clean( $path );

		// Is the path a folder?
		if ( !is_dir( $path ) )
		{
			die( 'Path is not a folder; Path: ' . $path );
		}

		// read the source directory
		$handle = opendir( $path );
		while ( ($file = readdir( $handle )) !== false )
		{
			if ( ($file != '.') && ($file != '..') && (!in_array( $file, $exclude )) )
			{
				$dir = $path . DS . $file;
				$isDir = is_dir( $dir );
				if ( $isDir )
				{
					if ( $recurse )
					{
						if ( is_integer( $recurse ) )
						{
							$arr2 = self::files( $dir, $filter, $recurse - 1, $fullpath );
						}
						else
						{
							$arr2 = self::files( $dir, $filter, $recurse, $fullpath );
						}

						$arr = array_merge( $arr, $arr2 );
					}
				}
				else
				{
					if ( preg_match( "/$filter/", $file ) )
					{
						if ( $fullpath )
						{
							$arr[] = $path . DS . $file;
						}
						else
						{
							$arr[] = $file;
						}
					}
				}
			}
		}
		closedir( $handle );
		asort( $arr );
		return $arr;

	}

	public static function Write( $File, $Content, $Mode = FILE_APPEND )
	{
		file_put_contents( $File, $Content, $Mode );

	}

}

/**
 * @deprecated since version 1.0
 */
class File extends XFile
{
	
}
