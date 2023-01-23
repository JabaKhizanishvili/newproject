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
class PageElementStatus extends PageElement
{

    /**
     * Element type
     *
     * @access	protected
     * @var		string
     */
    protected $_name = 'Status';

    public function fetchElement($row, $node, $config)
    {
        $key = trim($node->attributes('key'));
        $data = $this->getLibList();
        $Curent = C::_($row->{$key}, $data);
        if(!is_object($Curent))
        {
            return '';
        }
        if($row->STATUS == 1)
        {
            $html = '<div class=""><a href="?option=contractstatus&id=' . $row->ID . '">' . C::_('TITLE', $Curent) . '</a></div>';
        }
        else
        {
            $html = C::_('TITLE', $Curent);
        }
        return $html;
    }

    protected function getLibList()
    {
        static $data = null;
        if(is_null($data))
        {
            $query = 'select '
                    . ' t.lib_value id, '
                    . ' t.lib_title title '
                    . ' from lib_status t '
                    . ' where t.active=1 '
            //     . ' order by id asc'
            ;
            $data = DB::LoadObjectList($query, 'id');
        }

        return $data;
    }

}
