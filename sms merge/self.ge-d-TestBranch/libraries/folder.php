<?php
require_once 'file.php';

/**
 * @version		$Id: folder.php 16421 2010-04-24 23:57:12Z dextercowley $
 * @package		Joomla.Framework
 * @subpackage	FileSystem
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
// Check to ensure this file is within the rest of the framework
/**
 * A Folder handling class
 *
 * @static
 * @package 	Joomla.Framework
 * @subpackage	FileSystem
 * @since		1.5
 */
class XFolder
{
	/**
	 * Create a folder -- and all necessary parent folders.
	 *
	 * @param string A path to create from the base path.
	 * @param int Directory permissions to set for folders created.
	 * @return boolean True if successful.
	 * @since 1.5
	 */
	public static function create( $path = '', $mode = 0755 )
	{
		static $nested = 0;

		// Check to make sure the path valid and clean
		$path = File::clean( $path );

		// Check if parent dir exists
		$parent = dirname( $path );
		if ( !Folder::exists( $parent ) )
		{
			// Prevent infinite loops!
			$nested++;
			if ( ($nested > 20) || ($parent == $path) )
			{
				echo 'SOME_ERROR_CODE, Folder::create: ' . 'Infinite loop detected';
				$nested--;
				return false;
			}

			// Create the parent directory
			if ( Folder::create( $parent, $mode ) !== true )
			{
				// Folder::create throws an error
				$nested--;
				return false;
			}

			// OK, parent directory has been created
			$nested--;
		}

		// Check if dir already exists
		if ( Folder::exists( $path ) )
		{
			return true;
		}

		// We need to get and explode the open_basedir paths
		$obd = ini_get( 'open_basedir' );
		// If open_basedir is set we need to get the open_basedir that the path is in
		if ( $obd != null )
		{
			if ( DS != '/' )
			{
				$obdSeparator = ";";
			}
			else
			{
				$obdSeparator = ":";
			}
			// Create the array of open_basedir paths
			$obdArray = explode( $obdSeparator, $obd );
			$inBaseDir = false;
			// Iterate through open_basedir paths looking for a match
			foreach ( $obdArray as $test )
			{
				$test = File::clean( $test );
				if ( strpos( $path, $test ) === 0 )
				{
					$inBaseDir = true;
					break;
				}
			}
			if ( $inBaseDir == false )
			{
				// Return false for Folder::create because the path to be created is not in open_basedir
				echo 'Folder::create: ' . 'Path not in open_basedir paths';
				return false;
			}
		}

		// First set umask
		$origmask = umask( 0 );
		// Create the path
		if ( !$ret = mkdir( $path, $mode ) )
		{
			umask( $origmask );
			echo 'Folder::create: ' . 'Could not create directory. Path: ' . $path;
			return false;
		}

		// Reset umask
		umask( $origmask );
		return $ret;

	}

	/**
	 * Wrapper for the standard file_exists function
	 *
	 * @param string Folder name relative to installation dir
	 * @return boolean True if path is a folder
	 * @since 1.5
	 */
	public static function exists( $path )
	{
		return is_dir( File::clean( $path ) );

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
		$path = File::clean( $path );

		// Is the path a folder?
		if ( !is_dir( $path ) )
		{
			echo 'Folder::files: ' . 'Path is not a folder. Path: ' . $path;
			return false;
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
							$arr2 = Folder::files( $dir, $filter, $recurse - 1, $fullpath );
						}
						else
						{
							$arr2 = Folder::files( $dir, $filter, $recurse, $fullpath );
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

	/**
	 * Utility function to read the folders in a folder.
	 *
	 * @param	string	The path of the folder to read.
	 * @param	string	A filter for folder names.
	 * @param	mixed	True to recursively search into sub-folders, or an
	 * integer to specify the maximum depth.
	 * @param	boolean	True to return the full path to the folders.
	 * @param	array	Array with names of folders which should not be shown in
	 * the result.
	 * @return	array	Folders in the given folder.
	 * @since 1.5
	 */
	public static function folders( $path, $filter = '.', $recurse = false, $fullpath = false, $exclude = array( '.svn', 'CVS' ) )
	{
		// Initialize variables
		$arr = array();

		// Check to make sure the path valid and clean
		$path = File::clean( $path );

		// Is the path a folder?
		if ( !is_dir( $path ) )
		{
			echo 'Folder::folder: ' . 'Path is not a folder. Path: ' . $path;
			return false;
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
					// Removes filtered directories
					if ( preg_match( "/$filter/", $file ) )
					{
						if ( $fullpath )
						{
							$arr[] = $dir;
						}
						else
						{
							$arr[] = $file;
						}
					}
					if ( $recurse )
					{
						if ( is_integer( $recurse ) )
						{
							$arr2 = Folder::folders( $dir, $filter, $recurse - 1, $fullpath );
						}
						else
						{
							$arr2 = Folder::folders( $dir, $filter, $recurse, $fullpath );
						}

						$arr = array_merge( $arr, $arr2 );
					}
				}
			}
		}
		closedir( $handle );

		asort( $arr );
		return $arr;

	}

	/**
	 * Makes path name safe to use.
	 *
	 * @access	public
	 * @param	string The full path to sanitise.
	 * @return	string The sanitised string.
	 * @since	1.5
	 */
	public static function makeSafe( $path )
	{
		$ds = (DS == '\\') ? '\\' . DS : DS;
		$regex = array( '#[^A-Za-z0-9:\_\-' . $ds . ' ]#' );
		return preg_replace( $regex, '', $path );

	}

}

/**
 * @deprecated since version 1.0
 */
class Folder extends XFolder
{
	
}
