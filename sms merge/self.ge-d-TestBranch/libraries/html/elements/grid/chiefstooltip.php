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
class JGridElementChiefsTooltip extends JGridElement
{

    /**
     * Element type
     *
     * @access	protected
     * @var		string
     */
    protected $_name = 'chiefstooltip';

    public function fetchElement($row, $node, $group)
    {

        $chiefs = [];
        if (isset($this->getWorkerChiefs()[$row->ID])) {
            $chiefs = $this->getWorkerChiefs()[$row->ID];
        }

        if (empty($chiefs)) {
            return '';
        }

        $length = trim($node->attributes('length'));
        $sp = trim($node->attributes('word-separator'));
        $limit_type = trim($node->attributes('limit_type'), 0);

        return Helper::MakeToolTip(implode(', ', $chiefs), $length, $limit_type, $sp);
    }

    public function getWorkerChiefs()
    {

        static $workerChiefs = [];
        if ( empty( $workerChiefs ) ) {
            $query = "SELECT rwc.WORKER, sp.FIRSTNAME, sp.LASTNAME  
                    FROM REL_WORKER_CHIEF rwc 
                    LEFT JOIN SLF_PERSONS sp ON rwc.CHIEF_PID = sp.ID WHERE rwc.CLEVEL IN (0, 1)";

            foreach (DB::LoadObjectList( $query ) as $item) {

                if (!isset($workerChiefs[$item->WORKER])) {
                    $workerChiefs[$item->WORKER] = [];
                }

                $workerChiefs[$item->WORKER][] = XTranslate::_($item->FIRSTNAME) . ' ' . XTranslate::_($item->LASTNAME);
            }

        }

        return $workerChiefs;
    }

}
