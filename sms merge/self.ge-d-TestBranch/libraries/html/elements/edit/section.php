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
class JElementSection extends JElement
{

    /**
     * Element type
     *
     * @access	protected
     * @var		string
     */
    protected $_name = 'Section';

    public function fetchElement($name, $value, $node, $control_name)
    {
        $SectionID = 'params' . $name;
        $app = array('0' => Text::_('Section Filter'));
        $List = Helper::getSectionList();
        $options = array();
        $IDxList = array();
        foreach($app as $val => $text)
        {
            $options[] = HTML::_('select.option', $val, $text);
        }

        foreach($List as $item)
        {
            $disable = false;
            $IDxList[$item->DEPT_ID][] = $item->ID;
            $val = $item->ID;
            $text = $item->DEPT_TITLE . ' - ' . $item->TITLE;
            $options[] = HTML::_('select.option', $val, $text, 'value', 'text', $disable);
        }
        $js = 'var ' . $SectionID . '_list = new Array();' . "\n";
        $null = array(0);
        foreach($IDxList as $key => $IDx)
        {
            $IDx[] = 0;
            $disable = false;
            $null[] = implode(',', $IDx);
            $js .= $SectionID . '_list[' . $key . '] = [' . implode(',', $IDx) . '];' . "\n";
        }
        $js .= $SectionID . '_list[0] = [' . implode(',', $null) . '];' . "\n";

        Helper::SetJS($js, false);
        Helper::SetJS('DependentSelect("paramsDEPT_ID", "paramsSECTION_ID", ' . $SectionID . '_list' . ');');
        return HTML::_('select.genericlist', $options, $control_name . '[' . $name . ']', '', 'value', 'text', $value, $SectionID);
    }

}
