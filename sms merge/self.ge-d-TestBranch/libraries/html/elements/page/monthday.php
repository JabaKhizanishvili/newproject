<?php

/**
 * @version		$Id: monthday.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Framework
 * @copyright	Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
// Check to ensure this file is within the rest of the framework
/**
 * Renders a monthday element
 *
 * @package 	WSCMS.Framework
 * @subpackage		Parameter
 * @since		1.5
 */
class PageElementMonthDay extends PageElement
{

    /**
     * Element type
     *
     * @access	protected
     * @var		string
     */
    protected $_name = 'MonthDay';

    public function fetchElement($row, $node, $group)
    {
        return $row->LIB_DAY . ' ' . Text::_(date("F", mktime(0, 0, 0, $row->LIB_MONTH, 10)));
    }

}
