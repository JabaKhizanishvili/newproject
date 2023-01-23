<?php

/**
 * @version		$Id: list.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Framework
 * @copyright	Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
// Check to ensure this file is within the rest of the framework
/**
 * Renders a list element
 *
 * @package 	WSCMS.Framework
 * @subpackage		Parameter
 * @since		1.5
 */
class JElementApitables extends JElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'apitables';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$Tables = $this->getTables();
		$IN = [];
		if ( !empty( $value ) )
		{
			$IN = json_decode( $value );
		}
		$html = '<div class="checksBox">';
		foreach ( $Tables as $Key => $Table )
		{
			$columns = C::_( 'columns', $Table );
			$T_name = C::_( 'title', $Table );
			$ch1 = ' checked="checked" ';
			if ( !array_key_exists( $Key, $IN ) )
			{
				$ch1 = '';
			}
			$html .= '<div class="level_0 itemrow_' . $Key . ' radio radioparent" data-rel="' . $Key . '"><input type="checkbox" ' . $ch1 . ' class="self-border" name="params[' . $name . '][ON][]" id="params' . $name . '_' . $Key . '" value="' . $Key . '"><label for="params' . $name . '_' . $Key . '">' . Text::_( $T_name ) . '</label></div><div class="cls"></div>';
			foreach ( $columns as $column )
			{
				$ch2 = ' checked="checked" ';
				$arr = C::_( $Key, $IN );
				if ( !in_array( $column, $arr ) )
				{
					$ch2 = '';
				}
				$html .= '<div class="level_1 itemrow_' . $Key . ' radio"><input type="checkbox" ' . $ch2 . ' class="self-border" name="params[' . $name . '][' . $Key . '][]" id="params' . $name . '_' . $Key . '_' . $column . '" value="' . $column . '"><label for="params' . $name . '_' . $Key . '_' . $column . '"> ' . Text::_( $column ) . '</label></div><div class="cls"></div>';
			}
		}
		$html .= '</div>';
		return $html;

	}

	public function getTables()
	{
		$folder = PATH_BASE . DS . 'libraries' . DS . 'rest' . DS . 'tables_xml';
		if ( !is_dir( $folder ) )
		{
			return [];
		}

		$scan = Folder::files( $folder, '\.xml$', false, true );
		if ( !count( $scan ) )
		{
			return [];
		}
		$Tables = [];
		foreach ( $scan as $file )
		{
			$XMLFile = $this->loadXMLFile( $file );
			$table = File::stripExt( File::getName( $file ) );
			$Table[$table] = [];
			$Table[$table]['title'] = C::_( '@attributes.title', $XMLFile->attributes(), $table );
			$Cols = $XMLFile->getElementByPath( 'columns' )->children();
			foreach ( $Cols as $Col )
			{
				$Table[$table]['columns'][] = (string) $Col->attributes( 'name' );
			}
			$Tables = array_merge( $Tables, $Table );
		}
		return $Tables;

	}

	/**
	 * 
	 * @param type $path
	 * @return SimpleXMLElements
	 */
	public function loadXMLFile( $path )
	{
		if ( $path )
		{
			require_once PATH_BASE . DS . 'libraries' . DS . 'html' . DS . 'simplexml.php';
			$xml = new SimpleXML();
			if ( $xml->loadFile( $path ) )
			{
				return $xml->document;
			}
		}
		return false;

	}

}
