<?php
/**
 * @version		$Id: parameter.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Framework
 * @copyright	Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
// Check to ensure this file is within the rest of the framework
defined( 'PATH_BASE' ) or die();
require_once 'pages.php';

/**
 * Parameter handler
 *
 * @package 	WSCMS.Framework
 * @subpackage		Parameter
 * @since		1.5
 */
require_once dirname( __FILE__ ) . DS . 'registry.php';

class HistPage extends Page
{
	/**
	 * loaded elements
	 *
	 * @access	private
	 * @var		array
	 * @since	1.5
	 */
	protected $_allData = array();
	protected $_key = array();

	/**
	 * Constructor
	 *
	 * @access	protected
	 * @param	string The raw parms text
	 * @param	string Path to the xml setup file
	 * @since	1.5
	 */
	public function __construct( $data, $allData = array(), $key = 0, $path = '', $config = array() )
	{
		$this->_allData = $allData;
		$this->_key = $key;
		parent::__construct( $data, $path, $config );

	}

	/**
	 * Render all parameters
	 *
	 * @access	public
	 * @param	string	The name of the control, or the default text area if a setup file is not found
	 * @return	array	Aarray of all parameters, each as array Any array of the label, the form element and the tooltip
	 * @since	1.5
	 */
	public function getTableBody( $group = '_default' )
	{
		if ( !isset( $this->_xml[$group] ) )
		{
			return false;
		}
		$children = $this->_xml[$group]->children();
		?>
		<tbody>
			<?php
			if ( empty( $this->_data ) )
			{
				$msg = 'Record Not Found';
				Users::Redirect( '?ref=page_no_data', $msg );
			}
			foreach ( $children as $param )
			{
				$key = $param->attributes( 'key' );
				$changedKey = '';
				$changedValue = '';
				if ( $this->CompareData( $key ) == 'yes' )
				{
					$changedKey = ' changed_key';
					$changedValue = ' changed_hist';
				}
				?>
				<tr>
					<td>
						<div class="page_key<?php echo $changedKey; ?>">
							<?php echo Text::_( $param->attributes( 'label' ) ); ?>
						</div>
					</td>
					<td>
						<div class="page_value<?php echo $changedValue; ?>">
							<?php $this->getParam( $param, (object) $this->_data ); ?>
						</div>
					</td>
				</tr>
				<?php
			}
			?>
		</tbody>
		<?php

	}

	public function CompareData( $key )
	{
		$NextKey = $this->_key + 1;
		if ( isset( $this->_allData[$NextKey] ) )
		{
			$PrevData = $this->_allData[$NextKey];
			$CurrentValue = C::_( $key, $this->_data );
			$PrevValue = isset( $PrevData->{$key} ) ? $PrevData->{$key} : '';
			return ($CurrentValue == $PrevValue) ? '' : 'yes';
		}
		return '';

	}

}
