<?php

/**
 * @version		$Id: php.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Framework
 * @copyright	Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */

/**
 * PHP class format handler for Registry
 *
 * @package 	WSCMS.Framework
 * @subpackage		Registry
 * @since		1.5
 */
class RegistryFormatPHP extends RegistryFormat
{

    /**
     * Converts an object into a php class string.
     * 	- NOTE: Only one depth level is supported.
     *
     * @access public
     * @param object $object Data Source Object
     * @param array  $param  Parameters used by the formatter
     * @return string Config class formatted string
     * @since 1.5
     */
    public function objectToString($object, $params = null)
    {

        // Build the object variables string
        $vars = '';
        foreach(get_object_vars($object) as $k => $v)
        {
            if(is_scalar($v))
            {
                $vars .= "\tpublic $" . $k . " = '" . addcslashes($v, '\\\'') . "';\n";
            }
            elseif(is_array($v))
            {
                $vars .= "\tpublic $" . $k . " = " . $this->_getArrayString($v) . ";\n";
            }
        }

        $str = "<?php\nclass " . $params['class'] . "\n{\n";
        $str .= $vars;
        $str .= "}\n";

        return $str;
    }

    /**
     * Placeholder method
     *
     * @access public
     * @return boolean True
     * @since 1.5
     */
    public function stringToObject($data, $options = null)
    {
        return true;
    }

    protected function _getArrayString($a)
    {
        $s = 'array(';
        $i = 0;
        foreach($a as $k => $v)
        {
            $s .= ($i) ? ', ' : '';
            $s .= '"' . $k . '" => ';
            if(is_array($v))
            {
                $s .= $this->_getArrayString($v);
            }
            else
            {
                $s .= '"' . addslashes($v) . '"';
            }
            $i++;
        }
        $s .= ')';
        return $s;
    }

}
