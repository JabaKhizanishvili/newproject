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
class JElementIfworker extends JElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'ifworker';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		if (Helper::CheckTaskPermision('admin'))
		{
			return $this->Sworker( $name, $value, $node, $control_name );
		}
		else 
		{
			return $this->ChiefWorkers( $name, $value, $node, $control_name );
		}
	}
	
	public function ChiefWorkers( $name, $value, $node, $control_name )
	{
		$org = $node->attributes( 'limitorg' );
		$ORG = '';
		if ( $org == 1 )
		{
			$ORG = ' and t.org = ' . C::_( '_registry._default.data.ORG', $this->_parent );
		}

		$Depts = $this->getLibList( $ORG );

		$options[] = HTML::_( 'select.option', 0, Text::_( 'Workers FILTER' ) );
		foreach ( $Depts as $dept )
		{
			$val = $dept->ID;
			$text = $dept->TITLE;
			$options[] = HTML::_( 'select.option', $val, $text );
		}
		$html = HTML::_( 'select.genericlist', $options, $control_name . '[' . $name . ']', ' class="form-control search-select" ', 'value', 'text', $value, $control_name . $name );
		return $html;

	}
	
	public function Sworker( $name, $value, $node, $control_name )
	{
		$org = $node->attributes( 'limitorg' );
		$ORG = '';
		if ( $org == 1 )
		{
			if ( $get = Request::getVar( 'params', array() ) )
			{
				$ORG = '&org=' . $get['ORG'];
			}
			elseif ( $get = C::_( '_registry._default.data.ORG', $this->_parent ) )
			{
				$ORG = '&org=' . $get;
			}
		}

		$Link = '?option=s&service=ajaxworkers' . $ORG;
		$return = '<div class="WorkersBlock">';
		if ( $value )
		{
			$Worker = XGraph::GetOrgUser( $value );
			$return .= '<div class="WorkerContainerNew" id="WorkerContainerNew">'
							. '<div class="WorkertItem">'
							. '<div class="WorkerItem_name">' . C::_( 'FIRSTNAME', $Worker ) . ' ' . C::_( 'LASTNAME', $Worker ) . ' - ' . C::_( 'POSITION', $Worker ) . ' - ' . C::_( 'ORG_NAME', $Worker ) . '</div>'
							. '<input type="hidden" name="' . $control_name . '[' . $name . ']" id="' . $control_name . $name . '" value="' . C::_( 'ID', $Worker ) . '" />'
							. '<div class="cls"></div>'
							. '</div>'
							. '</div>';
		}
		else
		{
			$return .= '<div class="WorkerContainerNew" id="WorkerContainerNew"></div>';
		}
		$return .= '<div class="cls"></div>'
						. '<div class="input-group">'
						. '<input type="text" name="" id="' . $control_name . $name . '_autocomplete" class="kbd form-control" />'
						. '</div>'
						. '</div>'
						. '<script id="ajaxworkerTMPL" type="text/x-jquery-tmpl">'
						. '<div class="WorkertItem">'
						. '<div class="WorkerItem_name">${WORKER}</div>'
						. '<input type="hidden" name="' . $control_name . '[' . $name . ']" id="' . $control_name . $name . '" value="${ID}" />'
						. '<div class="cls"></div>'
						. '</div>'
						. '</script>';
		$JS = '$("#' . $control_name . $name . '_autocomplete").autocomplete('
						. '{'
						. 'minChars:3,'
						. 'autoSelectFirst:true,'
						. 'showNoSuggestionNotice:1,'
						. 'serviceUrl: "' . $Link . '",'
						. 'onSelect: function (worker) '
						. '{'
						. '$("#WorkerContainerNew").html("");'
						. 'var $Data = {WORKER: worker.value, ID:worker.data};'
						. '$("#ajaxworkerTMPL").tmpl($Data).appendTo("#WorkerContainerNew");'
						. '$("#' . $control_name . $name . '_autocomplete").val(""); '
						. '}'
						. '}'
						. ');';
		$JS .= 'var WorkerData = "' . $value . '";'
						. 'if(WorkerData!="")'
						. '{'
						. 'getWorker(WorkerData);'
						. '}';
		Helper::SetJS( $JS );
		return $return;

	}

	protected function getLibList( $ORG )
	{
		$query = 'select '
						. ' t.id, '
						. ' t.firstname || \' \' || t.lastname || \' - \' || t.org_name title '
						. ' from hrs_workers t '
						. ' where '
						. ' t.active = 1 '
						. ' and t.id in (select wc.worker from rel_worker_chief wc where wc.chief in (select m.id from hrs_workers m where m.PARENT_ID =  ' . Users::GetUserID() . ' )) '
						. $ORG
						. ' order by title asc';
		return DB::LoadObjectList( $query );

	}

}
