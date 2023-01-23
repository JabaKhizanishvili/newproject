<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$Return = Request::getVar( 'return' );
$UseExt = Helper::getConfig( 'extended_session_use' );
$AzureSSO = Helper::getConfig( 'azure_saml_sso_on' );
$AzureAuthName = Helper::getConfig( 'azure_saml_auth_tab_name' );
$AzureButtonName = Helper::getConfig( 'azure_saml_auth_button_name' );
$CommonAuthName = Helper::getConfig( 'common_auth_tab_name' );
$option = 'login';
?>
<div class="container-fluid">
	<div class="row">
		<div class="col-6 col-lg-4">
			<div class="page-container page-narrow page-login">
				<form action="?option=login" method="post" name="fform" id="fform" class="form-horizontal" role="form">
					<div class="login_form">
						<div class="form-group">
							<div class="slf-login-title">
								<?php echo Text::_( 'Login' ); ?>
							</div>
						</div>
						<?php
						if ( $AzureSSO )
						{
							?>
							<div class="tab-content">
								<div id="tab1" class="tab-pane fade row active in">
									<div class="form-group">
										<br />
									</div>
									<div class="form-group login-submit">
										<button class="btn btn-danger" type="button" onclick="doAction('<?php echo $option; ?>', 'saml_sso', 0, 0);">
											<!--<button type="submit" class=" ">-->
											<?php echo $AzureButtonName; ?>
										</button>
										<input type="hidden" value="" name="task" /> 
									</div>
									<div class="form-group">
										<br />
									</div>

								</div>
								<div id="tab2" class="tab-pane fade row">
									<?php
								}
								?>
								<div class="form-group">
									<input type="text" class="form-control" id="username" name="username" value="<?php echo Request::getVar( 'username', '' ); ?>" placeholder="<?php echo Text::_( 'UserName' ); ?>"/>
								</div>
								<div class="form-group from-group-right">
									<input class="form-control" id="password" name="password" type="password" value="" placeholder="<?php echo Text::_( 'Password' ); ?>" />
									<span class="pasdword-state from-group-addon-right">
										<i class="bi bi-eye-slash"></i>
									</span>
								</div>
								<?php
								if ( $UseExt )
								{
									?>
									<div class="form-group form-group-sm form-group-half text-right self-color radio">
										<input type="checkbox" value="1" name="remember" id="remember" class="self-border" />
										<label for="remember">
											<?php echo Text::_( 'Remember' ); ?>
										</label>
									</div>

									<?php
								}
								?>
								<div class="cls"></div>

								<div class="form-group login-submit">
									<button type="submit" class="btn btn-danger ">
										<?php echo Text::_( 'LOG-IN' ); ?>
									</button>
								</div>
								<?php
								if ( $AzureSSO )
								{
									?>
								</div>
							</div>
							<?php
						}
						?>
						<input type="hidden" name="return" id="return" value="<?php echo $Return; ?>" />
						<div class="cls"></div>
					</div>
					<?php
					if ( $AzureSSO )
					{
						?>
						<div class="row holids" style="margin-bottom: unset;">
							<ul class="nav nav-tabs tabs-below nav-justified">
								<li view="tab1" class="nav-item active">
									<a data-toggle="tab" href="#tab1" aria-expanded="true">
										<?php echo $AzureAuthName; ?>
									</a>
								</li>
								<li view="tab2" class="nav-item">
									<a data-toggle="tab" href="#tab2" aria-expanded="false">
										<?php echo $CommonAuthName; ?>
									</a>
								</li>
							</ul>
						</div>
						<?php
					}
					?>

				</form>
				<?php
				if ( XTranslate::ShowLangs() )
				{
					$Langs = XTranslate::GetLangs();
					$CurrentLang = XTranslate::GetCurrentLang();
					?>
					<div class="text-center">
						<?php
						$LandA = array();
						foreach ( $Langs as $Lang )
						{
							$Add = '';
							$Code = C::_( 'LIB_CODE', $Lang );
							if ( $Code == $CurrentLang )
							{
								$Add = 'active_lang';
							}
							ob_start();
							?>
							<a href="?setLanguage=<?php echo $Code; ?>"  class="<?php echo $Add; ?>">
								<?php echo C::_( 'LIB_TITLE', $Lang ); ?>
							</a>
							<?php
							$LandA[] = ob_get_clean();
						}
						echo implode( ' | ', $LandA );
						?>
					</div>
					<?php
				}
				?>
			</div>
		</div>
		<?php require_once X_PATH_BASE . DS . 'templates' . DS . 'z__slider.php'; ?>
	</div>
</div>
<?php
$this->setHelp( 'logout' );
