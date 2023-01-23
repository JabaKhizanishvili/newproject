<?php

/**
 * @version		$Id: grid.php 1 2011-07-13 05:09:23Z $
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
abstract class HTMLGrid
{

    /**
     * @param	string	The link title
     * @param	string	The order field for the column
     * @param	string	The current direction
     * @param	string	The selected ordering
     * @param	string	An optional task override
     */
    public static function sort($title, $order, $direction = 'asc', $selected = 0, $task = NULL)
    {
        $direction = mb_strtolower($direction);
        $images = array('sort_asc.png', 'sort_desc.png');
        $index = intval($direction == 'desc');
        $direction = ($direction == 'desc') ? 'asc' : 'desc';

        $html = '<a href="javascript:tableOrdering(\'' . $order . '\',\'' . $direction . '\',\'' . $task . '\');" title="' . Text::_('Click to sort this column') . '">';
        $html .= Text::_($title);
        if($order == $selected)
        {
            $html .= HTML::_('image.administrator', $images[$index], '/templates/images/', NULL, NULL);
        }
        $html .= '</a>';
        return $html;
    }

    /**
     * @param int The row index
     * @param int The record id
     * @param boolean
     * @param string The name of the form element
     *
     * @return string
     */
    public static function id($rowNum, $recId, $checkedOut = false, $name = 'cid')
    {
        if($checkedOut)
        {
            return '';
        }
        else
        {
            return '<input type="checkbox" id="cb' . $rowNum . '" name="' . $name . '[]" value="' . $recId . '" onclick="isChecked(this.checked);" />';
        }
    }

    public static function access($row, $i, $archived = NULL)
    {
        if(!$row->access)
        {
            $color_access = 'style="color: green;"';
            $task_access = 'accessregistered';
        }
        else if($row->access == 1)
        {
            $color_access = 'style="color: red;"';
            $task_access = 'accessspecial';
        }
        else
        {
            $color_access = 'style="color: black;"';
            $task_access = 'accesspublic';
        }

        if($archived == -1)
        {
            $href = Text::_($row->groupname);
        }
        else
        {
            $href = '
			<a href="javascript:void(0);" onclick="return listItemTask(\'cb' . $i . '\',\'' . $task_access . '\')" ' . $color_access . '>
			' . Text::_($row->groupname) . '</a>'
            ;
        }

        return $href;
    }

    public static function checkedOut($row, $i, $identifier = 'id')
    {
        $user = JFactory::getUser();
        $userid = $user->get('id');

        $result = false;
        if($row instanceof Table)
        {
            $result = $row->isCheckedOut($userid);
        }
        else
        {
            $result = Table::isCheckedOut($userid, $row->checked_out);
        }

        $checked = '';
        if($result)
        {
            $checked = HTMLGrid::_checkedOut($row);
        }
        else
        {
            $checked = HTML::_('grid.id', $i, $row->{$identifier});
        }

        return $checked;
    }

    public static function published($row, $i, $imgY = 'tick.png', $imgX = 'publish_x.png', $prefix = '')
    {
        $img = $row->published ? $imgY : $imgX;
        $task = $row->published ? 'unpublish' : 'publish';
        $alt = $row->published ? Text::_('Published') : Text::_('Unpublished');
        $action = $row->published ? Text::_('Unpublish Item') : Text::_('Publish item');

        $href = '
		<a href="javascript:void(0);" onclick="return listItemTask(\'cb' . $i . '\',\'' . $prefix . $task . '\')" title="' . $action . '">
		<img src="templates/images/' . $img . '" border="0" alt="' . $alt . '" /></a>'
        ;

        return $href;
    }

    public static function state($filter_state = '*', $published = 'Published', $unpublished = 'Unpublished', $archived = NULL, $trashed = NULL)
    {
        $state[] = HTML::_('select.option', '', '- ' . Text::_('Select State') . ' -');
        //Jinx : Why is this used ?
        //$state[] = HTML::_('select.option',  '*', Text::_( 'Any' ) );
        $state[] = HTML::_('select.option', 'P', Text::_($published));
        $state[] = HTML::_('select.option', 'U', Text::_($unpublished));

        if($archived)
        {
            $state[] = HTML::_('select.option', 'A', Text::_($archived));
        }

        if($trashed)
        {
            $state[] = HTML::_('select.option', 'T', Text::_($trashed));
        }

        return HTML::_('select.genericlist', $state, 'filter_state', 'class="form-control" size="1" onchange="submitform( );"', 'value', 'text', $filter_state);
    }

    public static function order($rows, $image = 'filesave.png', $task = "saveorder")
    {
        $image = HTML::_('image.administrator', $image, '/templates/images/', NULL, NULL, Text::_('Save Order'));
        $href = '<a href="javascript:saveorder(' . (count($rows) - 1) . ', \'' . $task . '\')" title="' . Text::_('Save Order') . '">' . $image . '</a>';
        return $href;
    }

    protected static function _checkedOut($row, $overlib = 1)
    {
        $hover = '';
        if($overlib)
        {
            $text = addslashes(htmlspecialchars($row->editor));

            $date = HTML::_('date', $row->checked_out_time, Text::_('DATE_FORMAT_LC1'));
            $time = HTML::_('date', $row->checked_out_time, '%H:%M');

            $hover = '<span class="editlinktip hasTip" title="' . Text::_('Checked Out') . '::' . $text . '<br />' . $date . '<br />' . $time . '">';
        }
        $checked = $hover . '<img src="templates/images/checked_out.png"/></span>';

        return $checked;
    }

}
