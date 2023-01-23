<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$params = '';
if ( $this->data )
{
	$params = HTML::convertParams( $this->data );
}
$ID = C::_( 'ID', $this->data );
?>
<div class="page_title">
	<?php echo Helper::getPageTitle(); ?>
	<div class="toolbar">
		<?php
		Helper::getToolbar( 'Save', $this->_option_edit, 'uploadbulletin' );
		Helper::getToolbar( 'Cancel', $this->_option_edit, 'cancel' );
		?>
	</div>
	<div class="cls"></div>
</div>
<div class="page_content">
	<div class="row">
		<div class="col-md-6">
			<?php
			$dateFormat = '%Y-%m-%d';
			$dateFields = [
					'START_DATE',
					'END_DATE',
			];
			$dinamics = [
					'WORKER' => 'slfstaffw',
					'ORG' => 'org',
					'START_DATE' => 'print',
					'END_DATE' => 'print',
//					'APPROVE' => 'slfstaff',
					'UCOMMENT' => 'print'
			];

			$collect = new stdClass();
			foreach ( $dinamics as $key => $value )
			{
				$v = C::_( $key, $this->data, '' );
				$collect->$key = $v;
				if ( in_array( $key, $dateFields ) )
				{
					$collect->$key = PDate::Get( $v )->toFormat( $dateFormat );
				}
			}
			Xhelp::DataBox( $collect, $dinamics, 'col-md-12', [], 'WORKER' );
			?>
		</div>
		<div class="col-md-6">
			<form action="?option=<?php echo $this->_option_edit; ?>" method="post" class="form-horizontal" name="fform" id="fform">
				<?php
				echo HTML::renderParams( $params, dirname( __FILE__ ) . DS . 'default_upload.xml' );
				?>

				<input type="hidden" name="params[ID]" id="paramsID" value="<?php echo $ID; ?>">
				<input type="hidden" value="save" name="task" /> 
			</form>
		</div>
	</div>
</div>
<?php
$this->setHelp();

