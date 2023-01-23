<?php
$Status = Helper::GetUserInOutStatus();
?>
<div class="profile_item_block">
	<div class="profile_title">
		<?php echo Text::_( 'Counting System' ); ?>
	</div>

	<div class="buttons-container" >
		<?php
		if ( $Status == 1 )
		{
			?>
			<span class="btn-LogOut">
				<?php
				Helper::getToolbar( 'System LogOut', $this->_option_edit, 'logout', 0, 1 );
				?>
			</span>
			<?php
		}
		else
		{
			?>
			<span class="btn-LogIn">
				<?php
				Helper::getToolbar( 'System LogIn', $this->_option_edit, 'login', 0, 1 );
				?>
			</span>
			<?php
		}
		?>
	</div>
	<form action="?option=<?php echo $this->_option_edit; ?>" method="post" class="form-horizontal" name="fform" id="fform">
		<input type="hidden" value="save" name="task" /> 
	</form>
</div>

