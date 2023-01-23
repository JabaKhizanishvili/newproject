<?php
/**
 * @version		$Id: Training.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Framework
 * @copyright	Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
// Check to ensure this file is within the rest of the framework

/**
 * Renders a Training element
 *
 * @package 	WSCMS.Framework
 * @subpackage		Parameter
 * @since		1.5
 */
class JElementTraining extends JElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'Training';

	public function fetchElement( $name, $valueIN, $node, $control_name )
	{
		$Trainings = SalaryHelper::getTrainingsList();
		$Html = '<input type="hidden" name="' . $control_name . '[' . $name . ']" id="' . $control_name . $name . '" value="' . $valueIN . '" />';
		$Html .= '<div class="form-control"><strong>' . C::_( $valueIN . '.LIB_TITLE', $Trainings ) . '</strong></div>';
		return $Html;

	}

}
