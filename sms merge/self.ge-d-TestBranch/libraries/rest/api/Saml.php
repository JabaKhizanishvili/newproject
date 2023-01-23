<?php

class XSAML extends XAPI
{
	protected $requestContentType = 'application/json';
	protected $StatusCode = 400;

	public function ACS()
	{
		session_start();
		$Auth = new Auth( );
		# If AuthNRequest ID need to be saved in order to later validate it, do instead
		# $ssoBuiltUrl = $auth->login(null, array(), false, false, true);
		$AuthNRequestID = Session::Get( 'AuthNRequestID' );
		if ( $AuthNRequestID )
		{
			$requestID = $AuthNRequestID;
		}
		else
		{
			$requestID = null;
		}
		try
		{
			$Auth->processResponse( $requestID );
		}
		catch ( Exception $ex )
		{
			XApiHelper::setHttpHeaders( $this->requestContentType, $this->StatusCode );
			$this->setError( $ex->getMessage() );
			exit();
		}

		$errors = $Auth->getErrors();

		if ( !empty( $errors ) )
		{
			XApiHelper::setHttpHeaders( $this->requestContentType, $this->StatusCode );
			$this->setError( implode( ', ', $errors ) );
		}
		if ( !$Auth->isAuthenticated() )
		{
			Users::Redirect( '/?ref=sso', 'Login Failed!', 'error' );
			exit();
		}
		Session::Set( 'AuthNRequestID', null );
		$attributes = $Auth->getAttributes();
		$User = array();
		$User['samlUserdata'] = $Auth->getAttributes();
		$User['samlNameId'] = $Auth->getNameId();
		$User['samlNameIdFormat'] = $Auth->getNameIdFormat();
		$User['samlNameIdNameQualifier'] = $Auth->getNameIdNameQualifier();
		$User['samlNameIdSPNameQualifier'] = $Auth->getNameIdSPNameQualifier();
		$User['samlSessionIndex'] = $Auth->getSessionIndex();
		foreach ( $attributes as $attributeName => $attributeValues )
		{
			$attributeName = File::getName( $attributeName );
			$User[$attributeName] = C::_( '0', $attributeValues );
		}
		return Users::SAMLSSOlogin( $User );

	}

	public function SLO()
	{
		session_start();
		$Auth = new Auth( );
		$requestID = $Auth->getLastRequestID();
		try
		{
			$Auth->processSLO( false, $requestID, true );
		}
		catch ( Exception $ex )
		{
			XApiHelper::setHttpHeaders( $this->requestContentType, $this->StatusCode );
			$this->setError( $ex->getMessage() );
			exit();
		}
		$errors = $Auth->getErrors();
		if ( !empty( $errors ) )
		{
			XApiHelper::setHttpHeaders( $this->requestContentType, $this->StatusCode );
			$this->setError( implode( ', ', $errors ) );
			exit();
		}
		Session::Destroy();
		Users::Redirect( '/?ref=samlsso', 'Logout Successfully!', 'msg' );
		return;

	}

	public function MetaData()
	{
		try
		{
			$Settings = new XSAMLSettings();
			// Now we only validate SP settings
			$metadata = $Settings->getSPMetadata();
			$errors = $Settings->validateMetadata( $metadata );
			if ( empty( $errors ) )
			{
				header( 'Content-Type: text/xml' );
				echo $metadata;
			}
			else
			{
				throw new Error( 'Invalid SP metadata: ' . implode( ', ', $errors ),
												OneLogin_Saml2_Error::METADATA_SP_INVALID );
			}
		}
		catch ( Exception $e )
		{
			echo $e->getMessage();
		}

	}

	public function setError( $Msg )
	{
		$Response = new stdClass();
		$Response->error = 1;
		$Response->message = $Msg;
		echo json_encode( $Response );

	}

}
