
<?php

class JElementAccessManagerRel extends JElement
{
	protected $_name = 'AccessManagerRel';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$data = array_flip( explode( ',', $value ) );
		$Controllers = $this->getControllers();
		$Offices = $this->getOfficeList();
		$K = '';
		ob_start();
		foreach ( $Controllers as $Cont )
		{
			$chk = '';
			if ( isset( $data[$Cont->ID] ) )
			{
				$chk = ' checked="checked" ';
			}
			if ( $Cont->OFFICE != $K )
			{
				?>
				<div class="">
					<?php echo C::_( $Cont->OFFICE . '.TITLE', $Offices ); ?>
				</div>

				<?php
				$K = $Cont->OFFICE;
			}
			?>
			<div class="level_0 radio">
				&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;-&nbsp;&nbsp;
				<input type="checkbox"  <?php echo $chk; ?> name="<?php echo $control_name; ?>[<?php echo $name; ?>][]" id="<?php echo $control_name . $name . '_' . $Cont->ID; ?>" class="self-border"  value="<?php echo $Cont->ID; ?>" />
				<label for="<?php echo $control_name . $name . '_' . $Cont->ID; ?>">
					<?php echo $Cont->LIB_TITLE; ?>
				</label>
			</div>
			<div class="cls"></div>
			<?php
		}
		$html = ob_get_clean();
		return $html;

	}

	public function getControllers()
	{
		$query = 'select '
						. ' t.id, '
						. ' t.office, '
						. ' t.lib_title '
						. ' from lib_doors t '
						. ' where t.active=1 '
						. ' order by t.office,  t.lib_title asc';
		return DB::LoadObjectList( $query );

	}

	public function getOfficeList()
	{
		$query = 'select '
						. ' id, '
						. ' t.lib_title title '
						. ' from lib_offices t '
						. ' where t.active=1 '
//						. ' order by title asc'
		;
		return DB::LoadObjectList( $query, 'ID' );

	}

//	public function OfficeName( $id, $array )
//	{
//		foreach ( $array as $key => $val )
//		{
//			if ( $val->ID == $id )
//			{
//				return ' (' . $array[$key]->TITLE . ')';
//			}
//		}
//		return null;
//
//	}

}
