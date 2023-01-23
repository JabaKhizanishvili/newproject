<?php
/**
 * @version		$Id: sql.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Framework
 * @copyright	Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
// Check to ensure this file is within the rest of the framework

/**
 * Renders a SQL element
 *
 * @package 	WSCMS.Framework
 * @subpackage		Parameter
 * @since		1.5
 */
class FilterElementWorker extends FilterElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'Worker';

	public function fetchElement( $name, $id, $node, $config )
	{
		$Link = '?option=s&service=ajaxworkers';
		$return = '<div class="WorkersBlockFilter">';
		$value = $this->GetConfigValue( $config['data'], $name );

		$return .= '<div class="WorkerContainerNew form-control" id="WorkerContainerNew"></div>';
		$return .= '<input type="text" name="" id="' . $id . '_autocomplete" class="kbd form-control workerFilerInput" style="display:none;" />'
						. '</div>'
						. '<script id="ajaxworkerTMPL" type="text/x-jquery-tmpl">'
						. '<div class="WorkertItem">'
						. '<div class="WorkerItem_name">${WORKER}</div>'
						. '<input type="hidden" name="' . $name . '" id="' . $id . '" value="${ID}" />'
						. '<div class="cls"></div>'
						. '</div>'
						. '</script>';
		$JS = '$("#' . $id . '_autocomplete").autocomplete('
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
						. '$("#' . $id . '_autocomplete").val(""); '
						. '$(".workerFilerInput", ".WorkersBlockFilter").hide(0);'
						. '$(".WorkerContainerNew", ".WorkersBlockFilter").show(0);'
						. 'setFilter();'
						. '}'
						. '}'
						. ');';
		$JS .= 'var WorkerData = "' . $value . '";'
						. 'if(WorkerData!="")'
						. '{'
						. 'getWorker(WorkerData);'
						. '}'
						. '$(".workerFilerInput", ".WorkersBlockFilter").hide(0);'
						. '$(".WorkersBlockFilter").click(function(){'
						. '$(".WorkerContainerNew", this).hide(0);'
						. '$(".workerFilerInput", ".WorkersBlockFilter").show(0).focus();'
						. '});';
		Helper::SetJS( $JS );
		return $return;

	}

}
