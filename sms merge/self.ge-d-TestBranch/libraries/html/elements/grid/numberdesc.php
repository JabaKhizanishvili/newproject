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
class JGridElementNumberdesc extends JGridElement
{

    /**
     * Element type
     *
     * @access	protected
     * @var		string
     */
    protected $_name = 'Numberdesc';

    public function fetchElement($row, $node, $config)
    {

		static $qty = null;
			
		
		if(is_null($qty)){
			$qty = $config['data']->total1;
		}

        $group = $this->GetConfigValue($config, '_option', 'default');
        $start = Request::getInt('start', $qty+1);
        static $number = array();
        if(isset($number[$group]))
        {
            --$number[$group];
            return '<b>' . $number[$group] . '<b>';
        }
        else
        {
            $number[$group] = $start;
            --$number[$group];
            return '<b>' . $number[$group] . '<b>';
        }
    }

}
