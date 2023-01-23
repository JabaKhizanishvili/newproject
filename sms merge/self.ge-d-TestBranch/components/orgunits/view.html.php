<?php

class orgunitsView extends View
{
	protected $_option = 'orgunits';
	protected $_option_edit = '';
	protected $_order = 'u.id';
	protected $_dir = '0';
	protected $_space = 'orgunits.display';

	function display( $tmpl = null )
	{
		/* @var $model orgunitsModel */
		$model = $this->getModel();
		/* @var $data ModelReturn */
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

	public function _PrintTree( $Item )
	{
		$function = Request::getCmd( 'js' );
		$Vars = Request::getVar( 'jsvar', array() );
		$VarsData = '';
		if ( count( $Vars ) )
		{
			$VarsData = ',' . implode( ',', $Vars );
		}
		?>
		<li>
			<?php
			$AStart = ' <a href="javascript:window.parent.' . $function . '(' . $Item->ID . $VarsData . ');window.parent.$.prettyPhoto.close();" >';
			$Aend = ' </a>';

			echo $AStart;
			echo $Item->LIB_TITLE;
			echo $Aend;
			if ( C::_( 'UNIT_TYPE', $Item ) )
			{
				?>
				<span class="unit-type">
					( <?php echo C::_( 'UNIT_TYPE', $Item ); ?> )
				</span>
				<?php
			}
			$Children = C::_( $Item->ID, $this->data->items, array() );
			if ( count( $Children ) )
			{
				?>
				<ul>
					<?php
					foreach ( $Children as $Child )
					{
						$this->_PrintTree( $Child, $this->data->items );
					}
					?>
				</ul>
				<?php
			}
			?>

		</li>

		<?php

	}

}
