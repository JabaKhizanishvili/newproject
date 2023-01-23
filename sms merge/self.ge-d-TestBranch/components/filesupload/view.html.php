<?php

class FilesUploadView extends View
{
	protected $_option = 'filesupload';
	protected $_option_edit = '';
	protected $_order = 'lib_title';
	protected $_dir = '0';
	protected $_space = 'fileupload.display';

	function display( $tmpl = null )
	{
		parent::display( $tmpl );

	}

}
