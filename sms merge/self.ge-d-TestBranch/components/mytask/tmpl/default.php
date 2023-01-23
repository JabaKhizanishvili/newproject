<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$config = get_object_vars( $this );
$TASK_ID = Collection::get( 'TASK_ID', $this->wdata );
$LIB_TASK_ID = Collection::get( 'LIB_TASK_ID', $this->wdata );
$LibTaskData = TaskHelper::getLibTask( $LIB_TASK_ID );
$WF_ID = Collection::get( 'WORKFLOW_ID', $this->wdata );
$isTaskUndone = (Collection::get( 'STATE', $this->wdata ) == 0);
$PostData = HTML::convertParams( Request::get( 'post' ) );
$Params = HTML::convertParams( Request::getVar( 'params' ) );
Request::setVar( 'xmode', C::_( 'XMODE', $this->wdata ) );
$Desc = C::_( 'TASK_DESCRIPTION', $this->wdata );
?>

<div class="page_title">
	<?php echo Helper::getPageTitle(); ?>
	<div class="toolbar">
		<?php
		if ( $isTaskUndone )
		{
			Helper::getModalIframeToolbar( 'Task Postpone', '?option=postpone&id=' . $WF_ID . '&tid=' . $TASK_ID, '99%', '99%' );
//			Helper::getToolbar( 'TakeTask', '', 'take' );
		}
		//	Helper::getModalIframeToolbar( 'Task Attribute', '?option=taskattr&id=' . $TASK_ID, '99%', '99%' );
		Helper::getModalIframeToolbar( 'Add Related Process', '?option=related&id=' . $WF_ID, '99%', '99%' );
		Helper::getModalIframeToolbar( 'Make Comment', '?option=comment&id=' . $WF_ID, '99%', '99%' );
		?>
	</div>
	<div class = "cls"></div>
</div>
<div class = "page_content">
	<form action = "?option=<?php echo $this->_option_edit; ?>" method = "post" name = "fform" id = "fform">
		<div class="container-fluid">
			<div class="col-sm-7">
				<?php
				if ( !Collection::get( 'STATE', $this->wdata ) )
				{
					?>
					<h2 class="text-center">
						<?php echo Text::_( 'Do Task' ); ?>
					</h2>
					<?php
					if ( !empty( $Desc ) )
					{
						?>
						<h3 class="text-left container">
							<?php echo nl2br($Desc); ?>
						</h3>
						<?php
					}

					$FLOW = Collection::get( 'ID', $this->wdata );
					TaskHelper::RenderAtributes( $LIB_TASK_ID, Request::getVar( 'attributes' ), $FLOW );
					if ( C::_( 'MUSTCOMMENT', $LibTaskData ) )
					{
						echo HTML::renderParams( $Params, dirname( __FILE__ ) . DS . 'approve_must.xml' );
					}
					else
					{
						echo HTML::renderParams( $Params, dirname( __FILE__ ) . DS . 'approve.xml' );
					}
				}

				$FLOW = Collection::get( 'ID', $this->wdata );
				$Attribs = TaskHelper::getFlowAttributes( $FLOW );
				?>
				<h2 class="text-center"></h2>
				<table  class="table table-striped table-hover  table-bordered">
					<thead>
						<tr>
							<th colspan="3" class="text-center">
								<h2>
									<?php
									echo Text::_( 'Task Attributes' );
									if ( !Collection::get( 'STATE', $this->wdata ) )
									{
										echo TaskHelper::getTaskResult( $LIB_TASK_ID, $TASK_ID, $FLOW );
									}
									elseif ( TaskHelper::CanEditAttribs( $FLOW ) )
									{
										echo TaskHelper::getEditButton( $TASK_ID, $FLOW );
									}
									?>
								</h2>		
							</th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<td colspan="10" class="text-right">
								<button type="button" class="btn btn-primary btn-sm " onclick="ToggleEmptyAttributes();">
									<?php echo Text::_( 'Toggle Empty Attributes' ); ?>
								</button>
							</td>
						</tr>
					</tfoot>
					<tbody>
						<?php
						foreach ( $Attribs as $Attrib )
						{
							$Attriobute = Collection::get( 'LIB_ATTRIBUTE', $Attrib );
							echo TaskHelper::RenderAtributeDisplay( $Attriobute, $Attrib, $FLOW );
						}
						?>
					</tbody>
				</table>
			</div>
			<div class="col-sm-5">
				<div class = "wf_data">
					<?php
//					if ( isset( $_SERVER['HTTP_USER_AGENT'] ) && $_SERVER['HTTP_USER_AGENT'] == 'Debug' )
//					{
//						echo '<pre>';
//						echo 'FILE: ' . __FILE__ . "\n";
//						echo 'Line: ' . __LINE__ . "\n";
//						echo '</pre>';
//						echo '<pre><pre>';
//						print_r( $this->wdata );
//						echo '</pre><b>FILE:</b> ' . __FILE__ . '     <b>Line:</b> ' . __LINE__ . '</pre>' . "\n";
//						die;
//					}
					echo HTML::renderPage( $this->wdata, dirname( __FILE__ ) . DS . 'view.xml', $config );
					?>
				</div>
			</div>
		</div>
		<div class="container-fluid">
			<div class="col-sm-7">
				<div class="task_item_comments">
					<?php echo TaskHelper::RenderComments( $FLOW ); ?>
				</div>
			</div>
			<div class="col-sm-5">

				<div class="task_item_logs">
					<?php echo TaskHelper::RenderLog( $FLOW ); ?>
				</div>
			</div>
		</div>
		<input type="hidden" value="save" name="task" /> 
		<input type="hidden" value="<?php echo Collection::get( 'ID', $this->wdata ); ?>" name="FLOW" /> 
		<input type="hidden" value="<?php echo Collection::get( 'TASK_ID', $this->wdata ); ?>" name="TASK_ID" /> 
		<input type="hidden" value="" name="ACTION_ID" /> 
		<input type="hidden" value="<?php echo $TASK_ID; ?>" name="nid[]" /> 
	</form>
</div>
<?php
$this->setHelp();

