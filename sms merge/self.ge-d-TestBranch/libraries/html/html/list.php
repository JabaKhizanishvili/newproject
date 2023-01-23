<?php

/**
 * @version		$Id: list.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Framework
 * @copyright	Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
// no direct access
defined('PATH_BASE') or die('Restricted access');

/**
 * Utility class for creating different select lists
 *
 * @static
 * @package 	WSCMS.Framework
 * @subpackage	HTML
 * @since		1.5
 */
abstract class HTMLList
{

    /**
     * Build the select list for access level
     */
    public static function accesslevel($row)
    {
        $db = JFactory::getDBO();

        $query = 'SELECT `id` AS `value`, `name` AS `text`'
                . ' FROM `#__groups`'
                . ' ORDER BY `id`'
        ;
        $db->setQuery($query);
        $groups = $db->loadObjectList();
        $access = HTML::_('select.genericlist', $groups, 'access', 'class="form-control" size="3"', 'value', 'text', intval($row->access), '', 1);

        return $access;
    }

    /**
     * Build the select list to choose an image
     */
    public static function images($name, $active = NULL, $javascript = NULL, $directory = NULL, $extensions = "bmp|gif|jpg|png")
    {
        if(!$directory)
        {
            $directory = '/templates/images/stories/';
        }

        if(!$javascript)
        {
            $javascript = "onchange=\"javascript:if (document.forms.adminForm." . $name . ".options[selectedIndex].value!='') {document.imagelib.src='..$directory' + document.forms.adminForm." . $name . ".options[selectedIndex].value} else {document.imagelib.src='../templates/images/blank.png'}\"";
        }

        jimport('joomla.filesystem.folder');
        $imageFiles = JFolder::files(JPATH_BASE . DS . $directory);
        $images = array(HTML::_('select.option', '', '- ' . Text::_('Select Image') . ' -'));
        foreach($imageFiles as $file)
        {
            if(preg_match("#$extensions#i", $file))
            {
                $images[] = HTML::_('select.option', $file);
            }
        }
        $images = HTML::_('select.genericlist', $images, $name, 'class="form-control" size="1" ' . $javascript, 'value', 'text', $active);

        return $images;
    }

    /**
     * Description
     *
     * @param string SQL with ordering As value and 'name field' AS text
     * @param integer The length of the truncated headline
     * @since 1.5
     */
    public static function genericordering($sql, $chop = '30')
    {
        $db = JFactory::getDBO();
        $order = array();
        $db->setQuery($sql);
        if(!($orders = $db->loadObjectList()))
        {
            if($db->getErrorNum())
            {
                echo $db->stderr();
                return false;
            }
            else
            {
                $order[] = HTML::_('select.option', 1, Text::_('first'));
                return $order;
            }
        }
        $order[] = HTML::_('select.option', 0, '0 ' . Text::_('first'));
        $cnt = count($orders);
        for($i = 0, $n = $cnt; $i < $n; $i++)
        {

            if(JString::strlen($orders[$i]->text) > $chop)
            {
                $text = JString::substr($orders[$i]->text, 0, $chop) . "...";
            }
            else
            {
                $text = $orders[$i]->text;
            }

            $order[] = HTML::_('select.option', $orders[$i]->value, $orders[$i]->value . ' (' . $text . ')');
        }
        $order[] = HTML::_('select.option', $orders[$i - 1]->value + 1, ($orders[$i - 1]->value + 1) . ' ' . Text::_('last'));

        return $order;
    }

    /**
     * Build the select list for Ordering of a specified Table
     */
    public static function specificordering($row, $id, $query, $neworder = 0)
    {
        $db = JFactory::getDBO();

        if($id)
        {
            $order = HTML::_('list.genericordering', $query);
            $ordering = HTML::_('select.genericlist', $order, 'ordering', 'class="form-control" size="1"', 'value', 'text', intval($row->ordering));
        }
        else
        {
            if($neworder)
            {
                $text = Text::_('descNewItemsFirst');
            }
            else
            {
                $text = Text::_('descNewItemsLast');
            }
            $ordering = '<input type="hidden" name="ordering" value="' . $row->ordering . '" />' . $text;
        }
        return $ordering;
    }

    /**
     * Select list of active users
     */
    public static function users($name, $active, $nouser = 0, $javascript = NULL, $order = 'name', $reg = 1)
    {
        $db = JFactory::getDBO();

        $and = '';
        if($reg)
        {
            // does not include registered users in the list
            $and = ' AND `gid` > 18';
        }

        $query = 'SELECT `id` AS `value`, `name` AS `text`'
                . ' FROM `#__users`'
                . ' WHERE `block` = 0'
                . $and
                . ' ORDER BY ' . $order
        ;
        $db->setQuery($query);
        if($nouser)
        {
            $users[] = HTML::_('select.option', '0', '- ' . Text::_('No User') . ' -');
            $users = array_merge($users, $db->loadObjectList());
        }
        else
        {
            $users = $db->loadObjectList();
        }

        $users = HTML::_('select.genericlist', $users, $name, 'class="form-control" size="1" ' . $javascript, 'value', 'text', $active);

        return $users;
    }

    /**
     * Select list of positions - generally used for location of images
     */
    public static function positions($name, $active = NULL, $javascript = NULL, $none = 1, $center = 1, $left = 1, $right = 1, $id = false)
    {
        if($none)
        {
            $pos[] = HTML::_('select.option', '', Text::_('None'));
        }
        if($center)
        {
            $pos[] = HTML::_('select.option', 'center', Text::_('Center'));
        }
        if($left)
        {
            $pos[] = HTML::_('select.option', 'left', Text::_('Left'));
        }
        if($right)
        {
            $pos[] = HTML::_('select.option', 'right', Text::_('Right'));
        }

        $positions = HTML::_('select.genericlist', $pos, $name, 'class="form-control" size="1"' . $javascript, 'value', 'text', $active, $id);

        return $positions;
    }

    /**
     * Select list of active categories for components
     */
    public static function category($name, $section, $active = NULL, $javascript = NULL, $order = 'ordering', $size = 1, $sel_cat = 1)
    {
        $multilang = JFactory::getMultiLanguage();
        $db = JFactory::getDBO();
        if($multilang->enabled())
        {
            $query = 'SELECT `id` AS `value`, `title` AS `text`'
                    . ' FROM `#__categories`'
                    . ' WHERE `lang`="' . $multilang->getShort() . '" AND  `section` = ' . $db->Quote($section)
                    . ' AND `published` = 1'
                    . ' ORDER BY ' . $order
            ;
        }
        else
        {
            $query = 'SELECT `id` AS `value`, `title` AS `text`'
                    . ' FROM `#__categories`'
                    . ' WHERE `section` = ' . $db->Quote($section)
                    . ' AND `published` = 1'
                    . ' ORDER BY ' . $order
            ;
        }
        $db->setQuery($query);

        $cats = $db->loadObjectList();
        $categories = array();
        if(is_array($cats))
        {
            if($sel_cat)
            {
                $categories[] = HTML::_('select.option', '0', '- ' . Text::_('Select a Category') . ' -');
                $categories = array_merge($categories, $cats);
            }
            else
            {
                $categories = $cats;
            }
        }

        $category = HTML::_('select.genericlist', $categories, $name, 'class="form-control" size="' . $size . '" ' . $javascript, 'value', 'text', $active);
        return $category;
    }

    /**
     * Select list of active sections
     */
    public static function section($name, $active = NULL, $javascript = NULL, $order = 'ordering', $uncategorized = true, $scope = 'content')
    {
        $multilang = JFactory::getMultiLanguage();
        $db = JFactory::getDBO();

        $categories[] = HTML::_('select.option', '-1', '- ' . Text::_('Select Section') . ' -');

        if($uncategorized)
        {
            $categories[] = HTML::_('select.option', '0', Text::_('Uncategorized'));
        }
        if($multilang->enabled())
        {
            $query = 'SELECT `id` AS `value`, `title` AS `text`'
                    . ' FROM `#__sections`'
                    . ' WHERE `lang`="' . $multilang->getShort() . '" AND `published` = 1'
                    . ' AND `scope` = ' . $db->Quote($scope)
                    . ' ORDER BY ' . $order
            ;
        }
        else
        {
            $query = 'SELECT `id` AS `value`, `title` AS `text`'
                    . ' FROM `#__sections`'
                    . ' WHERE `published` = 1'
                    . ' AND `scope` = ' . $db->Quote($scope)
                    . ' ORDER BY ' . $order
            ;
        }
        $db->setQuery($query);
        $secs = $db->loadObjectList();
        $sections = array();
        if(is_array($secs))
        {
            $sections = array_merge($categories, $secs);
        }
        $category = HTML::_('select.genericlist', $sections, $name, 'class="form-control" size="1" ' . $javascript, 'value', 'text', $active);

        return $category;
    }

    /**
     * Select list of countries
     */
    public static function countries($name, $active, $javascript = NULL)
    {

        $countriesList = JFactory::getCountries();
        $array = array();
        foreach($countriesList as $row)
        {
            $obj = new stdClass;
            $obj->value = $row->country;
            $obj->text = Text::_($row->country);
            $array[] = $obj;
        }


        $countries = array();
        $countries[] = HTML::_('select.option', '', '- ' . Text::_('Select Country') . ' -');
        $countries = array_merge($countries, $array);


        $countries = HTML::_('select.genericlist', $countries, $name, 'class="form-control" size="1" ' . $javascript, 'value', 'text', $active);

        return $countries;
    }

    /**
     * Select list of genders
     */
    public static function genders($name, $active, $javascript = NULL)
    {


        $genders = array();
        $genders[] = HTML::_('select.option', '', '- ' . Text::_('Select Gender') . ' -');
        $genders[] = HTML::_('select.option', 'M', Text::_('Male'));
        $genders[] = HTML::_('select.option', 'F', Text::_('Female'));

        $countries = HTML::_('select.genericlist', $genders, $name, 'class="form-control" size="1" ' . $javascript, 'value', 'text', $active);

        return $countries;
    }

    /**
     * Build the select list to choose a flag
     */
    public static function flags($name, $active = NULL, $javascript = NULL, $directory = NULL, $extensions = "bmp|gif|jpg|png")
    {
        static $imageFiles = null;

        if(!$directory)
        {
            $directory = 'templates/images/flags/';
        }

        if(!$javascript)
        {
            $javascript = "";
        }
        if(is_null($imageFiles))
        {
            jimport('joomla.filesystem.folder');
            jimport('joomla.filesystem.file');
            $imageFiles = JFolder::files(JPATH_BASE . DS . $directory);
        }
        $images = array(HTML::_('select.option', '', '- ' . Text::_('Select Flag') . ' -'));
        foreach($imageFiles as $file)
        {
            if(preg_match("#$extensions#i", $file))
            {
                $images[] = HTML::_('select.option', $directory . $file, JFile::stripExt($file));
            }
        }
        $images = HTML::_('select.genericlist', $images, $name, 'class="form-control" size="1" ' . $javascript, 'value', 'text', $active);

        return $images;
    }

}
