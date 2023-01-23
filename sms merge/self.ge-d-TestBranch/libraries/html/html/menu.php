<?php

/**
 * @version		$Id: menu.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Framework
 * @copyright	Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
// no direct access
defined('PATH_BASE') or die('Restricted access');

/**
 * Utility class working with menu select lists
 *
 * @static
 * @package 	WSCMS.Framework
 * @subpackage	HTML
 * @since		1.5
 */
abstract class HTMLMenu
{

    /**
     * Build the select list for Menu Ordering
     */
    public static function ordering($row, $id)
    {
        $multilang = JFactory::getMultiLanguage();
        $db = JFactory::getDBO();

        if($id)
        {
            if($multilang->enabled())
            {
                $query = 'SELECT `ordering` AS `value`, `name` AS `text`'
                        . ' FROM `#__menu`'
                        . ' WHERE `lang`="' . $multilang->getShort() . '" AND `menutype` = ' . $db->Quote($row->menutype)
                        . ' AND `parent` = ' . (int) $row->parent
                        . ' AND `published` != -2'
                        . ' ORDER BY `ordering`'
                ;
            }
            else
            {
                $query = 'SELECT `ordering` AS `value`, `name` AS `text`'
                        . ' FROM `#__menu`'
                        . ' WHERE `menutype` = ' . $db->Quote($row->menutype)
                        . ' AND `parent` = ' . (int) $row->parent
                        . ' AND `published` != -2'
                        . ' ORDER BY `ordering`'
                ;
            }
            $order = HTML::_('list.genericordering', $query);
            $ordering = HTML::_('select.genericlist', $order, 'ordering', 'class="form-control" size="1"', 'value', 'text', intval($row->ordering));
        }
        else
        {
            $ordering = '<input type="hidden" name="ordering" value="' . $row->ordering . '" />' . Text::_('DESCNEWITEMSLAST');
        }
        return $ordering;
    }

    /**
     * Build the multiple select list for Menu Links/Pages
     */
    public static function linkoptions($all = false, $unassigned = false)
    {
        $multilang = JFactory::getMultiLanguage();
        $db = JFactory::getDBO();

        // get a list of the menu items
        if($multilang->enabled())
        {
            $query = 'SELECT `m`.`id`, `m`.`parent`, `m`.`name`, `m`.`menutype`'
                    . ' FROM `#__menu` AS `m`'
                    . ' WHERE `m`.`lang`="' . $multilang->getShort() . '" AND `m`.`published` = 1'
                    . ' ORDER BY `m`.`menutype`, `m`.`parent`, `m`.`ordering`'
            ;
        }
        else
        {
            $query = 'SELECT `m`.`id`, `m`.`parent`, `m`.`name`, `m`.`menutype`'
                    . ' FROM `#__menu` AS `m`'
                    . ' WHERE `m`.`published` = 1'
                    . ' ORDER BY `m`.`menutype`, `m`.`parent`, `m`.`ordering`'
            ;
        }

        $db->setQuery($query);
        $mitems = $db->loadObjectList();
        $mitems_temp = $mitems;

        // establish the hierarchy of the menu
        $children = array();
        // first pass - collect children
        foreach($mitems as $v)
        {
            $id = $v->id;
            $pt = $v->parent;
            $list = @$children[$pt] ? $children[$pt] : array();
            array_push($list, $v);
            $children[$pt] = $list;
        }
        // second pass - get an indent list of the items
        $list = HTMLMenu::TreeRecurse(intval($mitems[0]->parent), '', array(), $children, 9999, 0, 0);

        // Code that adds menu name to Display of Page(s)
        $mitems_spacer = $mitems_temp[0]->menutype;

        $mitems = array();
        if($all | $unassigned)
        {
            $mitems[] = HTML::_('select.option', '<OPTGROUP>', Text::_('Menus'));

            if($all)
            {
                $mitems[] = HTML::_('select.option', 0, Text::_('All'));
            }
            if($unassigned)
            {
                $mitems[] = HTML::_('select.option', -1, Text::_('Unassigned'));
            }

            $mitems[] = HTML::_('select.option', '</OPTGROUP>');
        }

        $lastMenuType = null;
        $tmpMenuType = null;
        foreach($list as $list_a)
        {
            if($list_a->menutype != $lastMenuType)
            {
                if($tmpMenuType)
                {
                    $mitems[] = HTML::_('select.option', '</OPTGROUP>');
                }
                $mitems[] = HTML::_('select.option', '<OPTGROUP>', $list_a->menutype);
                $lastMenuType = $list_a->menutype;
                $tmpMenuType = $list_a->menutype;
            }

            $mitems[] = HTML::_('select.option', $list_a->id, $list_a->treename);
        }
        if($lastMenuType !== null)
        {
            $mitems[] = HTML::_('select.option', '</OPTGROUP>');
        }

        return $mitems;
    }

    public static function treerecurse($id, $indent, $list, &$children, $maxlevel = 9999, $level = 0, $type = 1)
    {
        if(@$children[$id] && $level <= $maxlevel)
        {
            foreach($children[$id] as $v)
            {
                $id = $v->id;

                if($type)
                {
                    $pre = '<sup>|_</sup>&nbsp;';
                    $spacer = '.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                }
                else
                {
                    $pre = '- ';
                    $spacer = '&nbsp;&nbsp;';
                }

                if($v->parent == 0)
                {
                    $txt = $v->name;
                }
                else
                {
                    $txt = $pre . $v->name;
                }
                $pt = $v->parent;
                $list[$id] = $v;
                $list[$id]->treename = "$indent$txt";
                $list[$id]->children = count(@$children[$id]);
                $list = HTMLMenu::TreeRecurse($id, $indent . $spacer, $list, $children, $maxlevel, $level + 1, $type);
            }
        }
        return $list;
    }

}