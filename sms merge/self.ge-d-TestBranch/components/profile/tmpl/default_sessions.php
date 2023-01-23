<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$config = get_object_vars( $this );
?>
<div class="page-container">
	<div class="page_title">
		<p class="p_title">
			<?php echo Text::_('active sessions'); ?>
		</p>
		<div class="toolbar">
			<?php
			Helper::getToolbar( 'delete', $this->_option, 'delete', 1, 1 );
			Helper::getToolbarExport( 'deleteall', $this->_option, 'deleteAll', 0, 1 );
			?>
		</div>
		<div class="cls"></div>
	</div>

	<div class="page_content">
		<form action="" method="get" name="fform" id="fform">
			<?php
			echo HTML::renderFilters( '', dirname( __FILE__ ) . DS . 'sessions.xml', $config );
			?>
			<div id="responsive_table">
				<?php
				echo HTML::renderGrid( $this->data->items, dirname( __FILE__ ) . DS . 'sessions.xml', $config );
				?>
			</div>

			<input type="hidden" value="<?php echo Request::getVar( 'option', DEFAULT_COMPONENT ); ?>" name="option" />
			<input type="hidden" value="<?php echo $this->data->order; ?>" name="order" id="order" />
			<input type="hidden" value="<?php echo $this->data->dir; ?>" name="dir"  id="dir"/>
			<input type="hidden" value="<?php echo $this->data->start; ?>" name="start"  id="start"/>
			<input type="hidden" value="<?php echo Request::getCmd( 'layout', '' ); ?>" name="layout" />
			<input type="hidden" value="" name="task" /> 
		</form>
	</div>
</div>
<?php
$this->setHelp();

