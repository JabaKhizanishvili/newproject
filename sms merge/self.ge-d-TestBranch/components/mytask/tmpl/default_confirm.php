
<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$config = get_object_vars( $this );
$TASK_ID = Collection::get( 'TASK_ID', $this->wdata );
$ActionID = Request::getInt( 'ACTION_ID' );
$Flow = $this->flow;
$FlowID = C::_( 'ID', $Flow );
?>
<div class="page_title">
	<?php echo Helper::getPageTitle(); ?> - <?php echo Text::_( 'Confirm Data' ); ?>
	<div class="toolbar">
		<?php
		?> <span class="red_button"> <?php Helper::getToolbar( 'Confirm', '', 'done', 0, 1 ); ?> </span> <?php
		Helper::getToolbar( 'Edit', '', 'corect' );
		Helper::getToolbar( 'Cancel', '', 'cancel' );
		?>
	</div>
	<div class="cls"></div>
</div>
<div class="page_content">
	<form action="?option=<?php echo $this->_option_edit; ?>" method="post" name="fform" id="fform" class="form-horizontal" role="form">
		<div class="task_actiors">
			<h1>
				<?php echo Text::_( 'Next tasks' ); ?> : 
			</h1>
			<?php
			echo Actiors::RenderActiors( $ActionID, $FlowID );
			?>
		</div>
		<div class="padding10"></div>
		<table class="table table-bordered table-striped">
			<tr>
				<td colspan="2" class="text-center">
					<h3>
						<?php echo Text::_( 'Task Attributes' ); ?>
					</h3>
				</td>
			</tr>
			<?php
			$Attribs = Collection::get( 'attributes', 'post', array() );
			foreach ( $Attribs as $Key => $Attrib )
			{
				echo TaskHelper::PreviewNewAttributes( $Key, $Attrib, $FlowID );
			}
			?>
			<tr>
				<td class="col-sm-6">
					<div class="text-right">
						<?php echo Text::_( 'comment' ); ?>
					</div>
				</td>
				<td class="col-sm-6">
					<?php echo nl2br( Collection::get( 'params.comment', 'post', '' ) ); ?>
				</td>
			</tr>
		</table>
		<div class="padding10"></div>
		<div class = "wf_data">
			<?php
			echo HTML::renderPage( $this->wdata, dirname( __FILE__ ) . DS . 'view.xml', $config );
			?>
		</div>
		<input type="hidden" value="save" name="task" /> 
		<?php
		$Data = Request::get( 'post' );
		$JsonData = array(
				'attributes' => C::_( 'attributes', $Data )
		);
		foreach ( $Data as $key => $value )
		{
			if ( $key == 'actiors' )
			{
				continue;
			}
			if ( is_array( $value ) )
			{
				foreach ( $value as $ikey => $ivalue )
				{
					if ( is_array( $ivalue ) )
					{
						foreach ( $ivalue as $skey => $svalue )
						{
							?>
							<input type="hidden" value="<?php echo htmlspecialchars( $svalue, ENT_QUOTES ); ?>" name="<?php echo $key; ?>[<?php echo $ikey; ?>][<?php echo $skey; ?>]" />
							<?php
						}
					}
					else
					{
						?>
						<input type="hidden" value="<?php echo htmlspecialchars( $ivalue, ENT_QUOTES ); ?>" name="<?php echo $key; ?>[<?php echo $ikey; ?>]" />
						<?php
					}
				}
			}
			else
			{
				?>
				<input type="hidden" value="<?php echo $value; ?>" name="<?php echo $key; ?>" /> 
				<?php
			}
		}
		?>
		<input type="hidden" value="<?php echo base64_encode( json_encode( $JsonData, JSON_OBJECT_AS_ARRAY ) ); ?>" name="JSONPostData" />
	</form>
</div>
<?php
$this->setHelp();
