<?php

/**
 * @version		$Id: ajaxgrid.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Framework
 * @copyright	Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */

/**
 * Utility class for creating HTML Grids
 *
 * @static
 * @package 	WSCMS.Framework
 * @subpackage	HTML
 * @since		1.5
 */
abstract class HTMLAjaxGrid
{

    public static function access($row, $i, $archived = NULL, $controller = '')
    {
        global $option;
        if(!$row->access)
        {
            $color_access = 'style="color: green;"';
            $task_access = 'ajaxaccessregistered';
            $action = Text::_('Set as Registered');
        }
        else if($row->access == 1)
        {
            $color_access = 'style="color: red;"';
            $task_access = 'ajaxaccessspecial';
            $action = Text::_('Set as Special');
        }
        else
        {
            $color_access = 'style="color: black;"';
            $task_access = 'ajaxaccesspublic';
            $action = Text::_('Set as Public');
        }

        if($archived == -1)
        {
            $href = Text::_($row->groupname);
        }
        else
        {
            $href = '<a href="javascript:void(0);" onclick="ajaxAccess(' . $row->id . ',\'' . $row->access . '\',\'' . $option . '\',\'' . $controller . '\');" title="' . $action . '" ' . $color_access . '>' . $row->groupname . '</a>';
        }

        return $href;
    }

    public static function published($row, $i, $controller = '', $imgY = 'tick.png', $imgX = 'publish_x.png', $prefix = '')
    {
        global $option;
        $img = $row->published ? $imgY : $imgX;
        $task = $row->published ? 'unpublish' : 'publish';
        $alt = $row->published ? Text::_('Published') : Text::_('Unpublished');
        $action = $row->published ? Text::_('Unpublish Item') : Text::_('Publish item');


        $href = '
			<a href="javascript:void(0);" onclick="ajaxPublish(' . $row->id . ',\'' . $row->published . '\',\'' . $option . '\',\'' . $controller . '\');">
				<img src="templates/images/' . $img . '" alt="' . $alt . '" title="' . $action . '" />
			</a>
			';
        return $href;
    }

    public static function blocked($row, $i, $controller = '', $imgY = 'tick.png', $imgX = 'publish_x.png', $prefix = '')
    {
        global $option;
        $img = $row->block ? $imgX : $imgY;
        $task = $row->block ? 'unblock' : 'block';
        $alt = $row->block ? Text::_('Blocked') : Text::_('Enabled');
        $action = $row->block ? Text::_('Unblock User') : Text::_('Block User');


        $href = '
			<a href="javascript:void(0);" onclick="ajaxBlock(' . $row->id . ',\'' . $row->block . '\',\'' . $option . '\',\'' . $controller . '\');">
				<img src="templates/images/' . $img . '" alt="' . $alt . '" title="' . $action . '" />
			</a>
			';
        return $href;
    }

}
