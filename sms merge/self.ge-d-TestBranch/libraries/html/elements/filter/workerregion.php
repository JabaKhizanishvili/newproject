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
class FilterElementWorkerRegion extends FilterElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'WorkerRegion';

	public function fetchElement( $name, $id, $node, $config )
	{
		$Link = '?option=s&service=ajaxworkersregion';
		$return = '<div class="WorkersBlock">';
		$value = $this->GetConfigValue( $config['data'], $name );

		$return .= '<div class="WorkerContainerNew" id="WorkerContainerNew"></div>';
		$return .= '<div class="cls"></div>'
						. '<div class="input-group">'
						. '<input type="text" name="" id="' . $id . '_autocomplete" class="kbd form-control" />'
						. '</div>'
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

}
