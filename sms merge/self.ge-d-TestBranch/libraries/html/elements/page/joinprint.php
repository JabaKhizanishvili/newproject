<?php

/**
 * @version		$Id: joinprint.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Framework
 * @copyright	Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
// Check to ensure this file is within the rest of the framework
/**
 * Renders a joinprint element
 *
 * @package 	WSCMS.Framework
 * @subpackage		Parameter
 * @since		1.5
 */
class PageElementJoinPrint extends PageElement
{

    /**
     * Element type
     *
     * @access	protected
     * @var		string
     */
    protected $_name = 'JoinPrint';

    public function fetchElement($row, $node, $group)
    {
        $patern = mb_strtolower(trim($node->attributes('patern')));
        foreach($row as $key => $value)
        {
            if(!is_object($value) || !is_array($value))
            {
                $patern = preg_replace('/\b' . mb_strtolower($key) . '\b/', $value, $patern);
            }
        }
        return $patern;
    }

}
