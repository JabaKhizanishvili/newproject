<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$config = get_object_vars( $this );
?>
<div class="page_title">
	<?php echo Helper::getPageTitle(); ?>
	<div class="toolbar">
		<?php
		Helper::getJSToolbar( 'Insert', 'InsertMultipleItems' );
		?>
	</div>
	<div class="cls"></div>
</div>

<div class="page_content">
	<form action="" method="get" name="fform" id="fform">
		<?php
		echo HTML::renderFilters( '', dirname( __FILE__ ) . DS . 'default.xml', $config );
		echo HTML::renderGrid( $this->data->items, dirname( __FILE__ ) . DS . 'default.xml', $config );
		?>
		<input type="hidden" value="<?php echo Request::getVar( 'option', DEFAULT_COMPONENT ); ?>" name="option" />
		<input type="hidden" value="<?php echo $this->data->order; ?>" name="order" id="order" />
		<input type="hidden" value="<?php echo $this->data->org; ?>" name="org" />
		<input type="hidden" value="<?php echo $this->data->dir; ?>" name="dir"  id="dir"/>
		<input type="hidden" value="<?php echo $this->data->groups; ?>" name="groups"  id="groups"/>
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
$this->setHelp();
