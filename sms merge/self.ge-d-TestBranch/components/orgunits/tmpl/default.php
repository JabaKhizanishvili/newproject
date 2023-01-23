<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$config = get_object_vars( $this );
?>
<div class="page_title">
	<?php echo Helper::getPageTitle(); ?>
	<div class="cls"></div>
</div>

<div class="page_content">
	<form action="" method="get" name="fform" id="fform">
		<?php
		echo HTML::renderFilters( '', dirname( __FILE__ ) . DS . 'default.xml', $config );
		if ( !empty( $this->data->items ) )
		{
			$function = Request::getCmd( 'js' );
			$Vars = Request::getVar( 'jsvar', array() );
			$VarsData = '';
			if ( count( $Vars ) )
			{
				$VarsData = ',' . implode( ',', $Vars );
			}
			?>
			<ul class="OrgTree">
				<?php
				$Level = 0;
				$Start = C::_( '0', $this->data->items );

				foreach ( $Start as $Item )
				{
					?>
					<li class="row">
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
				str_repeat( '</ul>', $Level );
				?>
			</ul>
			<?php
			Helper::SetJS( '$("ul.OrgTree").treemenu({openActive: true, closeOther:true, delay:400});' );
		}
		?>
		<input type="hidden" value="<?php echo Request::getVar( 'option', DEFAULT_COMPONENT ); ?>" name="option" />
		<input type="hidden" value="" name="task" /> 
		<input type="hidden" value="<?php echo Request::getCmd( 'tmpl', '' ); ?>" name="tmpl" /> 
		<input type="hidden" value="<?php echo Request::getCmd( 'js', '' ); ?>" name="js" />
		<?php
		$JSVars = Request::getVar( 'jsvar', array() );
		foreach ( $JSVars as $key => $value )
		{
			?>
			<input type="hidden" value="<?php echo $value; ?>" name="jsvar[<?php echo $key; ?>]" />
			<?php
		}
		?>
	</form>
</div>
<?php
