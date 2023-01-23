<?php

/**
 * @version		$Id: format.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Framework
 * @copyright	Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */

/**
 * Abstract Format for Registry
 *
 * @abstract
 * @package 	WSCMS.Framework
 * @subpackage	Registry
 * @since		1.5
 */
abstract class RegistryFormat
{

    /**
     * Returns a reference to a Format object, only creating it
     * if it doesn't already exist.
     *
     * @static
     * @param	string	$format	The format to load
     * @return	object	Registry format handler
     * @since	1.5
     */
    public static function getInstance($format)
    {
        static $instances;

        if(!isset($instances))
        {
            $instances = array();
        }

        $format = mb_strtolower(FilterInput::getInstance()->clean($format, 'word'));
        if(empty($instances[$format]))
        {
            $class = 'RegistryFormat' . $format;
            if(!class_exists($class))
            {
                $path = dirname(__FILE__) . DS . 'format' . DS . $format . '.php';
                if(file_exists($path))
                {
                    require_once($path);
                }
                else
                {
                    JError::raiseError(500, Text::_('Unable to load format class'));
                }
            }

            $instances[$format] = new $class ();
        }
        return $instances[$format];
    }

    /**
     * Converts an XML formatted string into an object
     *
     * @abstract
     * @access	public
     * @param	string	$data	Formatted string
     * @return	object	Data Object
     * @since	1.5
     */
    abstract public function stringToObject($data, $options = null);

    /**
     * Converts an object into a formatted string
     *
     * @abstract
     * @access	public
     * @param	object	$object	Data Source Object
     * @return	string	Formatted string
     * @since	1.5
     */
    abstract public function objectToString($object, $options = null);
}
