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
class FilterElementSText extends FilterElement
{

    /**
     * Element type
     *
     * @access	protected
     * @var		string
     */
    protected $_name = 'SText';

    public function fetchElement($name, $id, $node, $config)
    {
        $value = $this->GetConfigValue($config['data'], $name);
        $class = ( $node->attributes('class') ? 'class="form-control ' . $node->attributes('class') . '"' : 'class="form-control"' );
        $html = '<input ' . $class . ' value="' . $value . '" id = "' . $id . '" name = "' . $name . '" />';
        return $html;
    }

}
