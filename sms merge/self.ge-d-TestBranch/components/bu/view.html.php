<?php

class buView extends View
{
	protected $_option = 'bu';
	protected $_option_edit = 'bu';

	function display( $tmpl = null )
	{
		/* @var $model buModel */
		$params = (object) get_object_vars( $this );
		$model = $this->getModel( $params );
		$task = Request::getVar( 'task', '' );
		$data = array();
		switch ( $task )
		{
			case 'next':
				$data = Request::getVar( 'params', array() );
				$Result = $model->PrepareData( $data );
				if ( $Result )
				{
					$this->assignRef( 'DATA', $Result );
					$tmpl = 'confirm';
				}
				break;
			case 'save':
				$data = Request::getVar( 'params', array() );
				$link = '?option=' . $this->_option;
				if ( $model->SaveData( $data ) )
				{
					XError::setMessage( 'Data Saved!' );
					Users::Redirect( $link );
				}
				XError::setError( 'data_incorrect' );
				Users::Redirect( $link );
				break;
			case 'delete':
				$link = '?option=' . $this->_option;
				$data = Request::getVar( 'nid', array() );
				if ( empty( $data ) )
				{
					XError::setError( 'items_not_selected' );
					Users::Redirect( $link );
				}
				if ( $model->Delete( $data ) )
				{
					XError::setMessage( 'Data Deleted!' );
					Users::Redirect( $link );
				}
				XError::setError( 'Data_Not_Deleted!' );
				Users::Redirect( $link );
				break;

			case 'cancel':
				$data = Request::getVar( 'params', array() );
				$link = '?option=' . $this->_option;
				XError::setMessage( 'Action Canceled!' );
				Users::Redirect( $link );
				break;
			case 'changestate':
				$link = '?option=' . $this->_option;
				Users::Redirect( $link );
				break;

			default:
				$data = $model->getItem();
				break;
		}
		if ( !is_object( $data ) )
		{
			$data = (object) $data;
		}
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

	protected static function _GetHTML( $ID, $GlobalName, $Params )
	{
		ob_start();
		?>
		<tr>
			<?php
			foreach ( $Params as $Key => $Value )
			{
				?>
				<td>
					<input type="text" name="<?php echo $GlobalName; ?>[<?php echo $ID; ?>][<?php echo $Key; ?>]" value="<?php echo $Value; ?>" id="ncf-field-name<?php echo $Key; ?>" class="ncf-field form-control" />
				</td>
				<?php
			}
			?>
			<td>
				<a class="x-button-close" onclick="jQuery(this).parent().parent().remove();">X</a>
			</td>
		</tr>
		<?php
		/*
		 * 
		 * 		<td>Cell4</td>
		  <div id="item_graph_block<?php echo C::_( 'ID', $Params ); ?>" class="item_graph_block row">
		  <div class="col-md-2 pw_field">
		  <div class="form-control">
		  <a href="<?php echo $link; ?>" target="_blank" style="display: block;">
		  <?php echo File::getName( C::_( 'UFILE', $Params ) ); ?>
		  </a>
		  </div>
		  <input type="hidden" name="<?php echo $GlobalName; ?>[<?php echo C::_( 'ID', $Params ); ?>][UFILE]" value="<?php echo C::_( 'UFILE', $Params ); ?>" id="ncf-field-name<?php echo C::_( 'ID', $Params ); ?>" class="ncf-field form-control" />
		  </div>
		  <div class="col-md-2 pw_field">
		  <input type="text" name="<?php echo $GlobalName; ?>[<?php echo C::_( 'ID', $Params ); ?>][TIN]" value="<?php echo C::_( 'TIN', $Params ); ?>" id="ncf-field-name<?php echo C::_( 'TIN', $Params ); ?>" class="ncf-field form-control" placeholder="<?php echo Text::_( 'TIN' ); ?>" />
		  </div>
		  <div class="col-md-2 pw_field">
		  <input type="text" name="<?php echo $GlobalName; ?>[<?php echo C::_( 'ID', $Params ); ?>][MARK]" value="<?php echo C::_( 'MARK', $Params ); ?>" id="ncf-field-name<?php echo C::_( 'MARK', $Params ); ?>" class="ncf-field form-control" placeholder="<?php echo Text::_( 'MARK' ); ?>" />
		  </div>
		  <div class="col-md-2 pw_field">
		  <input type="text" name="<?php echo $GlobalName; ?>[<?php echo C::_( 'ID', $Params ); ?>][TYPE]" value="<?php echo C::_( 'TYPE', $Params ); ?>" id="ncf-field-name<?php echo C::_( 'TYPE', $Params ); ?>" class="ncf-field form-control" placeholder="<?php echo Text::_( 'TYPE' ); ?>" />
		  </div>
		  <div class="col-md-2 pw_field">
		  <input type="text" name="<?php echo $GlobalName; ?>[<?php echo C::_( 'ID', $Params ); ?>][BOX]" value="<?php echo C::_( 'BOX', $Params ); ?>" id="ncf-field-name<?php echo C::_( 'BOX', $Params ); ?>" class="ncf-field form-control" placeholder="<?php echo Text::_( 'BOX' ); ?>" />
		  </div>
		  <div class="col-md-2 pw_field">
		  <input type="text" name="<?php echo $GlobalName; ?>[<?php echo C::_( 'ID', $Params ); ?>][CDATE]" value="<?php echo C::_( 'CDATE', $Params ); ?>" id="ncf-field-name<?php echo C::_( 'CDATE', $Params ); ?>" class="ncf-field form-control" placeholder="<?php echo Text::_( 'CDATE' ); ?>" />
		  </div>

		  <div class="cls"></div>
		  </div>
		 */
		?>
		</tr>
		<?php
		$Content = ob_get_clean();
		return $Content;

	}

	protected static function _GetHTMLHeader( $Headers )
	{
		ob_start();
		?>
		<tr class="bulletin_report_item_head">
			<?php
			foreach ( $Headers as $Header )
			{
				?>
				<th><?php echo Text::_( $Header ); ?></th>
				<?php
			}
			?>
			<th>X</th>
		</tr>
		<?php
		$Content = ob_get_clean();
		return $Content;

	}

}
