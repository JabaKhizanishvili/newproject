<?php
/**
 * @version		$Id: filesystem.php 1 2011-09-21 05:19:43Z t.kevlishvili$
 * @package	Tools.Backuper
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

/**
 * A Folder handling class
 *
 * @static
 * @package 	Joomla.Framework
 * @subpackage	FileSystem
 * @since		1.5
 */
class FileSystem
{
	/**
	 * Wrapper for the standard file_exists function
	 *
	 * @param string Folder name relative to installation dir
	 * @return boolean True if path is a folder
	 * @since 1.5
	 */
	function exists( $path )
	{
		return is_dir( FileSystem::clean( $path ) );

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
	function files( $path, $filter = '.', $recurse = true, $fullpath = true, $exclude = array( '.svn', 'CVS' ) )
	{
		// Initialize variables
		$arr = array();
		// Check to make sure the path valid and clean
		$path = FileSystem::clean( $path );
		// Is the path a folder?
		if ( !is_dir( $path ) )
		{
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
							$arr2 = FileSystem::files( $dir, $filter, $recurse - 1, $fullpath );
						}
						else
						{
							$arr2 = FileSystem::files( $dir, $filter, $recurse, $fullpath );
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
	function folders( $path, $filter = '.', $recurse = true, $fullpath = true, $exclude = array( '.svn', 'CVS' ) )
	{
		// Initialize variables
		$arr = array();
		// Check to make sure the path valid and clean
		$path = FileSystem::clean( $path );
		// Is the path a folder?
		if ( !is_dir( $path ) )
		{
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
							$arr2 = FileSystem::folders( $dir, $filter, $recurse - 1, $fullpath );
						}
						else
						{
							$arr2 = FileSystem::folders( $dir, $filter, $recurse, $fullpath );
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
	 * Delete a folder.
	 *
	 * @param string The path to the folder to delete.
	 * @return boolean True on success.
	 * @since 1.5
	 */
	function DeleteFile( $path )
	{
		// Sanity check
		if ( !$path )
		{
			// Bad programmer! Bad Bad programmer!
			return false;
		}

		// Check to make sure the path valid and clean
		$path = FileSystem::clean( $path );

		// Remove the files
		if ( @unlink( $path ) !== true )
		{
			return false;
		}
		return true;

	}

	/**
	 * Makes path name safe to use.
	 *
	 * @access	public
	 * @param	string The full path to sanitise.
	 * @return	string The sanitised string.
	 * @since	1.5
	 */
	function makeSafe( $path )
	{
		$ds = (DS == '\\') ? '\\' . DS : DS;
		$regex = array( '#[^A-Za-z0-9:\_\-' . $ds . ' ]#' );
		return preg_replace( $regex, '', $path );

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
	function clean( $path, $ds = DS )
	{
		$path = trim( $path );
		if ( empty( $path ) )
		{
			$path = JPATH_BASE;
		}
		else
		{
			// Remove double slashes and backslahses and convert all slashes and backslashes to DS
			$path = preg_replace( '#[/\\\\]+#', $ds, $path );
		}
		return $path;

	}

}
