<?php
require_once PATH_BASE . DS . 'libraries' . DS . 'Units.php';

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
class JElementCategorybenefit extends JElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'categorybenefit';
	protected $Values = [];
	protected $error = 0;

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$name = $node->attributes( 'setname' );
		$action = C::_( '_registry._default.data.CHANGE_SUB_TYPE', $this->_parent );
		$ebnefit_types = C::_( '_registry._default.data.' . $name, $this->_parent );
		if ( !empty( $ebnefit_types ) )
		{
			if ( is_array( $ebnefit_types ) )
			{
				$ebnefit_types = json_decode( implode( '|', $ebnefit_types ) );
			}
			else
			{
				$ebnefit_types = json_decode( $ebnefit_types );
			}

			if ( isset( $ebnefit_types->ERROR ) )
			{
				$this->error = 1;
				unset( $ebnefit_types->ERROR );
			}
		}

		echo '<script>var identifiers = {}; var benefit_end_dates = {};
function changeBenefit(element, id){
  var set = $(element).val();
  var value1 = identifiers[set];
    $(\'#ncf-field-IDENTIFIER\' + id).parent().children(\'.label-must\').remove();
	$(\'#ncf-field-IDENTIFIER\' + id).parent(".pw_field").hide();
	$(\'#ncf-field-IDENTIFIER\' + id).prop( "disabled", true );
  if(value1 == 1 || value1 == 2)
	{
	$(\'#ncf-field-IDENTIFIER\' + id).parent(".pw_field").show();
	$(\'#ncf-field-IDENTIFIER\' + id).prop( "disabled", false );
	}
  if (value1 == 1)
  {
    $(\'<span class="label-must"><i class="bi bi-asterisk form_must_fill"></i></span>\').insertBefore(\'#ncf-field-IDENTIFIER\' + id);
  }

  var value2 = benefit_end_dates[set];
    $(\'#ncf-field-BENEFIT_END_DATE\' + id).parent().children(\'.label-must\').remove();
	$(\'#ncf-field-BENEFIT_END_DATE\' + id).parent(".pw_field").hide();
	$(\'#ncf-field-BENEFIT_END_DATE\' + id).prop( "disabled", true );
  if(value2 == 1 || value2 == 2)
	{
	$(\'#ncf-field-BENEFIT_END_DATE\' + id).parent(".pw_field").show();
	$(\'#ncf-field-BENEFIT_END_DATE\' + id).prop( "disabled", false );
	}
  if (value2 == 1)
  {
    $(\'<span class="label-must"><i class="bi bi-asterisk form_must_fill"></i></span>\').insertBefore(\'#ncf-field-BENEFIT_END_DATE\' + id);
  }
}</script>';

		$Workers = (array) explode( ',', $value );
		foreach ( $Workers as $worker )
		{
			if ( empty( $worker ) )
			{
				continue;
			}

			if ( in_array( $action, [ 2, 3 ] ) && !property_exists( $ebnefit_types, (int) $worker ) )
			{
				continue;
			}

			$worker_data = XGraph::getWorkerDataSch( (int) $worker );
			$worker_name = XTranslate::_( C::_( 'FIRSTNAME', $worker_data ), 'person' ) . ' ' . XTranslate::_( C::_( 'LASTNAME', $worker_data ), 'person' );

			$valueIN = C::_( $worker, $ebnefit_types, [] );
			$html = '';
			$html .= '<div class="items_spacer">' . $worker_name . '</div>';
			$html .= '<div class="worker_benefit_binding">';
			$html .= $this->Set_GetHTML( $name, $valueIN, $node, $control_name, $worker, $action );
			$html .= '</div>';

			echo $html;
		}

	}

	protected function Set_GetHTML( $name, $valueIN, $node, $control_name, $worker, $action = 1 )
	{
		$GlobalName = $control_name . '[' . $name . ']' . '[' . $worker . ']';
		ob_start();
		$JSs = '';
		?>
		<div class="pw_externalItemsBlock">
			<div id="PWBlock" class="PWBlock<?php echo $worker; ?>">
				<?php
				if ( $valueIN )
				{
					foreach ( $valueIN as $Key => $Item )
					{
						if ( $action != 3 )
						{
							$worker = (int) $Key;
						}
						if ( is_object( $Item ) )
						{
							$Item = (array) $Item;
						}
						if ( empty( $Item ) )
						{
							$Item = array();
						}
						$Item['id'] = $Key;
						echo $this->_GetHTML( $GlobalName, $Item, $worker, $node, $action );

						$JSs .= 'SetAlertFeatures("' . $Key . '");';
						$JSs .= '$("#ncf-field-BENEFIT' . $Key . '").val("' . C::_( 'BENEFIT', $Item, 0 ) . '");';
					}
				}
				else
				{
					$Params = array(
							'id' => '0',
					);
					echo $this->_GetHTML( $GlobalName, $Params, $worker, $node, $action );
					$JSs .= 'SetAlertFeatures(0);';
				}
				echo '<script id = "PWContainer' . $worker . '" type = "text/x-jquery-tmpl">';
				$Params = array(
						'id' => '${id}',
				);
				echo $this->_GetHTML( $GlobalName, $Params, $worker, $node, $action );
				echo '</script>';
				?>
			</div>
			<?php
			if ( $action == 1 )
			{
				?>
				<div class="Graphcontainercontrol" id="Graphcontainercontrol">
					<button onclick="XaddPWExternal('#PWContainer<?php echo $worker; ?>', '.PWBlock<?php echo $worker; ?>', {}, '<?php echo $worker; ?>')" type="button" class="x-bind-add">
						<?php echo Text::_( 'Add' ); ?>
					</button>
				</div>
			<?php } ?>
			<div class="cls"></div>
		</div>
		<?php
		$return = ob_get_clean();
		Helper::SetJS( $JSs );
		return $return;

	}

	protected function _GetHTML( $GlobalName, $Params, $worker, $node, $action = 1 )
	{
		$ii = (string) $worker . C::_( 'id', $Params );
		$dd = $worker . C::_( 'id', $Params );
		static $NN = 0;
		ob_start();
		$Select = $this->CategoryBenefit();
		$Selected = C::_( 'ACTIVE_BENEFIT', $Params, C::_( 'BENEFIT', $Params, -1 ) );
		?>
		<div id="item_graph_block<?php echo $dd; ?>" class="item_graph_block row">
			<?php
			$IDD = (int) C::_( 'ID', $Params, 0 );
			if ( $IDD > 0 )
			{
				?>
				<input type="hidden" name="<?php echo $GlobalName; ?>[<?php echo $dd; ?>][ID]" value="<?php echo $IDD; ?>">
				<?php
			}

			if ( $action == 2 )
			{
				if ( $this->error == 0 )
				{
					$Params = [];
				}

				$SS = C::_( C::_( 'ACTIVE_BENEFIT', $Params, $Selected ) . '.TITLE', $Select );
				$this->Freeze( 'ACTIVE_BENEFIT', $SS, $GlobalName . '[' . $dd . ']', $Selected );
			}
			if ( $action == 3 )
			{
				$ch_date = str_replace( '|', '_', $Selected ) . $dd;
				?>
				<div class="col-md-1 pw_field" style="width:3%;">
					<?php $this->add_label( '&nbsp' ); ?>
					<input id="checkbox_id<?php echo $dd; ?>" type="checkbox" name="params[BENEFIT_TYPES][<?php echo $worker; ?>][<?php echo $dd; ?>][DELETE]" value="value" style="display: none;">
					<label class="x-bind-check" ch_date="<?php echo $ch_date; ?>" for="checkbox_id<?php echo $dd; ?>" onClick="bindCheck(this);"><i class="bi bi-check"></i></label>
				</div>
				<?php
				$this->Freeze( 'BENEFIT', C::_( $Selected . '.TITLE', $Select ), $GlobalName . '[' . $dd . ']', $Selected );
				$this->Freeze( 'IDENTIFIER', C::_( 'IDENTIFIER', $Params ) );
				$this->Freeze( 'BENEFIT_START_DATE_T', C::_( 'CHANGE_DATE', $Params ) );
				$this->Freeze( 'BENEFIT_END_DATE_T', C::_( 'BENEFIT_END_DATE', $Params ) );
				?>
				<div id="<?php echo $ch_date; ?>" class="col-md-2 pw_field " style="display: none;" disabled>
					<?php $this->add_label( 'REMOVE_DATE', 1 ); ?>
					<div class="bfh-datepicker calendar_input_style x-bind ncf-field-date<?php echo $dd; ?>" data-mode="24h" data-date="" id="ncf-field-CHANGE_DATE<?php echo $dd; ?>" data-name="<?php echo $GlobalName; ?>[<?php echo $dd; ?>][CHANGE_DATE]" data-format="d-m-y"></div>
				</div>
				<?php
			}
			else
			{
				?>
				<div class="col-md-2 pw_field">
					<?php
					$this->add_label( 'BENEFIT' );
					$options = array();
					$options[] = HTML::_( 'select.option', -1, Text::_( 'select category' ) );
					$identifiers = [];
					$benefit_end_dates = [];
					foreach ( $Select as $id => $data )
					{
						$identifiers[$id] = C::_( 'IDENTIFIER', $data, 0 );
						$benefit_end_dates[$id] = C::_( 'BENEFIT_END_DATE', $data, 0 );

						if ( !$this->CheckCategory( $Selected, $id, $action ) )
						{
							continue;
						}

						$title = C::_( 'TITLE', $data );
						$options[] = HTML::_( 'select.option', $id, $title );
					}
					$val = $action == 3 || $this->error == 1 ? $Selected : -1;
					Helper::SetJS( 'identifiers = ' . json_encode( $identifiers ) . ';' );
					Helper::SetJS( 'benefit_end_dates = ' . json_encode( $benefit_end_dates ) . '; ' );
					echo HTML::_( 'select.genericlist', $options, $GlobalName . '[' . $dd . '][BENEFIT]', ' class="ncf-field form-control ' . $ii . '-m" onchange="changeBenefit(this, \'' . $ii . '\');" ', 'value', 'text', $val, 'ncf-field-BENEFIT' . $dd );
					?>
				</div>
				<div class = "col-md-2 pw_field" style="display: none;">
					<?php $this->add_label( 'IDENTIFIER' );
					?>
					<input type="text" name="<?php echo $GlobalName; ?>[<?php echo $dd; ?>][IDENTIFIER]" value="<?php echo C::_( 'IDENTIFIER', $Params ); ?>" id="ncf-field-IDENTIFIER<?php echo $dd; ?>" class="x-bind ncf-field<?php echo $worker; ?> form-control" />
				</div>
				<div class="col-md-2 pw_field">
					<?php $this->add_label( 'BENEFIT_START_DATE_T', 1 ); ?>
					<div class="bfh-datepicker calendar_input_style x-bind ncf-field-date<?php echo $dd; ?>" data-mode="24h" data-date="<?php echo C::_( 'CHANGE_DATE', $Params ); ?>" id="ncf-field-CHANGE_DATE<?php echo $dd; ?>" data-name="<?php echo $GlobalName; ?>[<?php echo $dd; ?>][CHANGE_DATE]" data-format="d-m-y"></div>
				</div>
				<div class="col-md-2 pw_field" style="display: none;">
					<?php $this->add_label( 'BENEFIT_END_DATE_T' ); ?>
					<div class="bfh-datepicker calendar_input_style x-bind ncf-field-date<?php echo $dd; ?>" data-mode="24h" data-date="<?php echo C::_( 'BENEFIT_END_DATE', $Params ); ?>" id="ncf-field-BENEFIT_END_DATE<?php echo $dd; ?>" data-name="<?php echo $GlobalName; ?>[<?php echo $dd; ?>][BENEFIT_END_DATE]" data-format="d-m-y"></div>
				</div>
				<?php
				if ( $action == 1 )
				{
					?>
					<div class="col-md-1 pw_field">
						<?php $this->add_label( '&nbsp' ); ?>
						<a class="x-bind-close" onclick="jQuery(this).parent().parent().remove();">X</a>
					</div>
					<?php
				}
			}
			?>
			<div class="cls"></div><br>
		</div>
		<?php
		$Content = ob_get_clean();
		$NN++;
		if ( $this->error == 1 )
		{
			Helper::SetJS( 'setTimeout(()=>{changeBenefit("#ncf-field-BENEFIT' . $dd . '", "' . $ii . '");}, 500);' );
		}

		return $Content;

	}

	public function add_label( $name, $must = 0 )
	{
		$html = '<label id="params' . $name . '-lbl" for="params' . $name . '">' . Text::_( $name ) . '</label>';
		$html .= $must == 1 ? '<span class="label-must"><i class="bi bi-asterisk form_must_fill"></i></span>' : '';
		echo $html;

	}

	public function CategoryBenefit()
	{
		static $CategoryBenefits = null;
		if ( is_null( $CategoryBenefits ) )
		{
			$Query = 'select '
							. ' c.id category_id, '
							. ' c.lib_title category, '
							. ' c.identifier, '
							. ' c.benefit_end_date, '
							. ' t.id benefit_type_id, '
							. ' t.lib_title benefit_type, '
							. ' t.lib_desc '
							. ' from lib_f_benefits c '
							. ' left join lib_f_benefit_types t on t.benefit = c.id '
							. ' where '
							. ' c.active = 1 '
							. ' and t.active = 1 '
							. ' and t.regularity in (1, 3)'
							. ' order by c.lib_title asc '
			;
			$result = DB::LoadObjectList( $Query );
			$collect = [];
			foreach ( $result as $key => $val )
			{
				$collect[$val->CATEGORY_ID . '|' . $val->BENEFIT_TYPE_ID]['TITLE'] = XTranslate::_( $val->CATEGORY ) . ' - ' . XTranslate::_( $val->BENEFIT_TYPE ) . ' (' . $val->LIB_DESC . ')';
				$collect[$val->CATEGORY_ID . '|' . $val->BENEFIT_TYPE_ID]['IDENTIFIER'] = $val->IDENTIFIER;
				$collect[$val->CATEGORY_ID . '|' . $val->BENEFIT_TYPE_ID]['BENEFIT_END_DATE'] = $val->BENEFIT_END_DATE;
			}

			$CategoryBenefits = $collect;
		}

		return $CategoryBenefits;

	}

	public function Freeze( $name, $value, $input = '', $input_val = '' )
	{
		?>
		<div class="col-md-2 pw_field">
			<?php $this->add_label( $name ); ?>
			<div class="form-control"><strong><?php echo $value; ?></strong></div>
		</div>
		<?php
		if ( !empty( $input ) )
		{
			?>
			<input type="hidden" name="<?php echo $input . '[' . $name . ']'; ?>" id="params<?php echo $name; ?>" value="<?php echo $input_val; ?>">
			<?php
		}

	}

	public function CheckCategory( $selected, $incoming, $action )
	{
		if ( $action != 2 )
		{
			return true;
		}

		$a = C::_( 0, explode( '|', $selected ), 'a' );
		$b = C::_( 0, explode( '|', $incoming ), 'a' );
		if ( $a != $b )
		{
			return false;
		}

		return true;

	}

}
