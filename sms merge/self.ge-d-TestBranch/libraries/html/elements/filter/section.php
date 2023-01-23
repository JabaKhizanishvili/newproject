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
class FilterElementSection extends FilterElement
{

    /**
     * Element type
     *
     * @access	protected
     * @var		string
     */
    protected $_name = 'Section';

    public function fetchElement($name, $SectionID, $node, $config)
    {
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
        $value = $this->GetConfigValue($config['data'], $name);
        Helper::SetJS($js, false);
        Helper::SetJS('DependentSelect("department_dept_id", "' . $SectionID . '", ' . $SectionID . '_list' . ');');
        return HTML::_('select.genericlist', $options, $name, ' onchange="setFilter();" class="form-control" ', 'value', 'text', $value, $SectionID);
    }

}
