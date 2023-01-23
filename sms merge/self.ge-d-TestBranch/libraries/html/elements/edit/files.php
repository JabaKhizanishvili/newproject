<?php
/**
 * @version		$Id: Files.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Framework
 * @copyright	Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
// Check to ensure this file is within the rest of the framework

/**
 * Renders a Files element
 *
 * @package 	WSCMS.Framework
 * @subpackage		Parameter
 * @since		1.5
 */
class JElementFiles extends JElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'Files';

	public function fetchElement( $name, $valueIN, $node, $control_name )
	{
		$width = '99%';
		$height = '99%';
		$id = substr( md5( rand( 0, 999999999 ) . microtime( 1 ) ), rand( 1, 20 ), 7 );
		$uri = URI::getInstance();
		$uri->setQuery( '' );
		$uri->setVar( 'option', 'filesupload' );
		$uri->setVar( 'tmpl', 'modal' );
		$uri->setVar( 'param', $id );
		$uri->setVar( 'js', 'getUploadFile' );
		$uri->setVar( 'width', $width );
		$uri->setVar( 'iframe', 'true' );
		$uri->setVar( 'height', $height );
		ob_start();
		if ( !empty( $valueIN ) && !is_array( $valueIN ) )
		{
			$valueIN = explode( '|', $valueIN );
		}
		$description = 'დასაშვებია მხოლოდ დოკუმენტის (.pdf, .docx, .xlsx), სურათის (.jpg, .jpeg, .png) ან არქივის (.zip, .rar) ფორმატის ფაილის ატვირთვა';
		?>
		<div class="uploadfilesblock">
			<input type="hidden" name="files_nem" id="<?php echo $control_name . $name; ?>" value="<?php echo $control_name . '[' . $name . ']'; ?>" class="uploadFilesdata_<?php echo $id; ?>" />
			<div class="container-fluid">
				<div class="col-md-6 nopadding uploadFilesButtons">
					<a class="btn btn-success i_btn_file" data-lity href="<?php echo $uri->toString(); ?> ">
						<i class="bi bi-folder2-open btn-ico"></i> 
						<?php echo Text::_( 'Upload' ); ?>
					</a>
					<div class="cls"></div>
				</div>
				<div class="col-md-6">
					<div class="form_desc red_desc_parent">
						<i class="bi bi-exclamation-lg exclamation-ico"></i>
						<span class="form_param_desc red_desc">
							<?php echo XTranslate::_( $description ); ?>
						</span>
					</div>
				</div>
			</div>
			<div class="uploadFilescontainer" id="uploadFilescontainer_<?php echo $id; ?>">
				<?php
				if ( is_array( $valueIN ) )
				{
					foreach ( $valueIN as $File )
					{
						$fileName = substr( $File, 33 );
						$href = 'download/?f=' . $File;
						?>
						<div class="uploadFileItem">
							<div class="uploadFileItem_name">
								<a href="<?php echo $href; ?>" target="_blank" class="upload_title"><?php echo $fileName; ?></a>
								<input type="hidden" name="<?php echo $control_name . '[' . $name . ']'; ?>[]" value="<?php echo $File; ?>"/>
							</div>
							<div class="uploadFiletools">
								<span class="uploadFiletool uploadFiletool_delete">
									<a  href="javascript:void(0);" onclick="RemoveFileItem(this);" class="upload_view">
										<!--<img src="templates/images/delete.gif" alt="View" />-->
										<i class="bi bi-x-lg X-ico"></i>
									</a>
								</span>
								<span class="uploadFiletool uploadFiletool_search">
									<a  href="<?php echo $href; ?>" target="_blank" class="upload_view">
										<!--<img class="bi-search" src="templates/images/view.png" alt="View" />-->
										<i class="bi bi-search search-ico"></i>
									</a>
								</span>
							</div>
							<div class="cls"></div>
						</div>
						<?php
					}
				}
				?>
			</div>
			<div class="cls"></div>
		</div>
		<?php
		$return = ob_get_clean();
		$JS = '$("a[rel^=\'iframe-' . $id . '\']") . prettyPhoto();';
//		$JS .= 'var filesData = "' . $valueIN . '";';
		Helper::SetJS( $JS );
		return $return;

	}

}
