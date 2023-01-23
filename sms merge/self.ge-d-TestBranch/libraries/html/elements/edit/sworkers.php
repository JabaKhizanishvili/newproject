<?php

class JElementSWorkers extends JElement
{
	var $_name = 'Workers';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$IDx = array();
		$return = '<div class="WorkersBlock">';
		if ( !empty( $value ) )
		{
			$Workers = $this->GetWorkers( $value );
			$index = 1;
			ob_start();
			foreach ( $Workers as $Worker )
			{
				$IDx[] = $Worker->ID;
				?>
				<div class="ContractItem">
					<div class="ContractItem_name"  title="<?php echo $Worker->FIRSTNAME . ' ' . $Worker->LASTNAME; ?> - <?php echo $Worker->POSITION; ?>">
						<?php
						echo $index . '&nbsp;&nbsp;&nbsp;';
						echo XTranslate::_( $Worker->FIRSTNAME, 'person' ) . ' ' . XTranslate::_( $Worker->LASTNAME, 'person' );
						?>
						<small> - 
							<?php echo XTranslate::_( $Worker->POSITION ); ?>
						</small>
						<small> - 
							<?php echo XTranslate::_( $Worker->PRIVATE_NUMBER ); ?>
						</small>
					</div>
					<div class="cls"></div>
				</div>
				<?php
				$index++;
			}
			$return .= ob_get_clean();
		}
		else
		{
			$return .= '<div class="ContractItem">' . Text::_( 'All Workers' ) . '<div class="cls"></div></div>';
		}
		$return .= ' <input type="hidden" name="' . $control_name . '[' . $name . ']" id="' . $control_name . $name . '" value="' . implode( ',', $IDx ) . '"  />'
						. '</div>';

		return $return;

	}

	public function GetWorkers( $value )
	{
		$Query = 'select '
						. ' w.id, '
						. ' w.firstname, '
						. ' w.lastname, '
						. ' w.position, '
						. ' w.private_number '
						. ' from hrs_workers_sch w '
						. ' left join hrs_table t on t.worker = w.id '
						. ' where '
						. ' t.id in( ' . $value . ' ) '
						. ' order by w.firstname '
		;
		return DB::LoadObjectList( $Query );

	}

}
