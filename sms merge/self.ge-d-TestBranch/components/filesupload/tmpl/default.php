<div class="page_title">
	<?php echo Helper::getPageTitle(); ?>
	<div class="cls"></div>
</div>
<div class="contentCenter error">
	<?php echo Text::_( 'UPLOAD_FILES_TERM' ); ?>
</div>

<div class="page_content">
	<form enctype="multipart/form-data" action="/uploads/" method="post" id="uploads" class="upload" multiple="1">
		<div id="drops" class="drop">
			<?php echo Text::_( 'Drop Here Files' ); ?>
			<a><?php echo Text::_( 'Browse Files' ); ?></a>
			<input type="file"  name="upl" multiple="" class="skip_this"  />
		</div>
		<ul>

		</ul>
		<input type="hidden" value="save" name="task" /> 
		<input type="hidden" value="<?php echo Request::getVar( 'js', '' ); ?>" name="js" /> 
		<div class="text-center">
			<button id="done_upload" class="btn btn-success btn-lg" type="button"><?php echo Text::_( 'Done Upload' ); ?></button>
		</div>
		<input type="hidden" value="" id="files_data" />
		<input type="hidden" value="<?php echo Request::getVar( 'param', '' ); ?>" id="param" name="param" />
	</form>
</div>
<?php
