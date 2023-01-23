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
class PageElementParty extends PageElement
{

    /**
     * Element type
     *
     * @access	protected
     * @var		string
     */
    protected $_name = 'party';

    public function fetchElement($row, $node, $config)
    {
        $key = trim($node->attributes('key'));
        $FieldDataU = $this->getParty($row->$key, $row->ID);
        $html = '<div class="page_party_data">';
        foreach($FieldDataU as $Value)
        {
            $Value = trim($Value);
            if(empty($Value))
            {
                continue;
            }
            $html .= '<div class="page_party_data_item">';
            $html .= $Value;
            $html .= '</div>';
        }
        $html .= '</div>';
        return $html;
    }

    public function getParty($idx, $ID)
    {
        static $data = array();
        $SID = md5($idx);
        if(isset($data[$SID]))
        {
            return $data[$SID];
        }
        $da = preg_replace('/[^0-9,]+/', '', $idx);
        $daX = implode('\',\'', explode(',', $da));
        $query = 'select decode(k.org_name, null, k.party, k.party || \' - \' || k.org_name) party '
                . ' from (select to_char(p.party) party, to_char(tp.ORG_NAME) ORG_NAME '
                . ' from rel_contract_party p '
                . ' left join (select nvl(t.org_name, null) org_name, t.tin '
                . ' from v_taxpayers t '
                . ' where t.tin in (\'' . $daX . '\')) tp '
                . ' on tp.tin = p.party '
                . ' where p.contract_id = \'' . $ID . '\') k '
        ;
        $Result = DB::LoadList($query);
        $data[$SID] = $Result;
        return $Result;
    }

}
