<?php
/**
 * This file is part of php-saml.
 *
 * (c) OneLogin Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package OneLogin
 * @author  OneLogin Inc <saml-info@onelogin.com>
 * @license MIT https://github.com/onelogin/php-saml/blob/master/LICENSE
 * @link    https://github.com/onelogin/php-saml
 */

/**
 * Configuration of the OneLogin PHP Toolkit
 */
class XSAMLSettings
{
	/**
	 * List of paths.
	 *
	 * @var array
	 */
	private $_paths = array();

	/**
	 * @var string
	 */
	private $_baseurl;

	/**
	 * Strict. If active, PHP Toolkit will reject unsigned or unencrypted messages
	 * if it expects them signed or encrypted. If not, the messages will be accepted
	 * and some security issues will be also relaxed.
	 *
	 * @var bool
	 */
	private $_strict = true;

	/**
	 * Activate debug mode
	 *
	 * @var bool
	 */
	private $_debug = false;

	/**
	 * SP data.
	 *
	 * @var array
	 */
	private $_sp = array();

	/**
	 * IdP data.
	 *
	 * @var array
	 */
	private $_idp = array();

	/**
	 * Compression settings that determine
	 * whether gzip compression should be used.
	 *
	 * @var array
	 */
	private $_compress = array();

	/**
	 * Security Info related to the SP.
	 *
	 * @var array
	 */
	private $_security = array();

	/**
	 * Setting contacts.
	 *
	 * @var array
	 */
	private $_contacts = array();

	/**
	 * Setting organization.
	 *
	 * @var array
	 */
	private $_organization = array();

	/**
	 * Setting errors.
	 *
	 * @var array
	 */
	private $_errors = array();

	/**
	 * Valitate SP data only flag
	 *
	 * @var bool
	 */
	private $_spValidationOnly = false;

	/**
	 * Initializes the settings:
	 * - Sets the paths of the different folders
	 * - Loads settings info from settings file or array/object provided
	 *
	 * @param array|null $settings         SAML Toolkit Settings
	 * @param bool       $spValidationOnly Validate or not the IdP data
	 *
	 * @throws Error If any settings parameter is invalid
	 * @throws Exception If Settings is incorrectly supplied
	 */
	public function __construct( array $settings = null, $spValidationOnly = false )
	{
		$this->_spValidationOnly = $spValidationOnly;
		if ( !isset( $settings ) )
		{
			if ( !$this->_loadSettingsFromFile() )
			{
				throw new XSAMLError(
												'Invalid file settings: %s',
												XSAMLError::SETTINGS_INVALID,
												array( implode( ', ', $this->_errors ) )
				);
			}
			$this->_addDefaultValues();
		}
		else
		{
			if ( !$this->_loadSettingsFromArray( $settings ) )
			{
				throw new XSAMLError(
												'Invalid array settings: %s',
												XSAMLError::SETTINGS_INVALID,
												array( implode( ', ', $this->_errors ) )
				);
			}
		}

		$this->formatIdPCert();
		$this->formatSPCert();
		$this->formatSPKey();
		$this->formatSPCertNew();
		$this->formatIdPCertMulti();

	}

	/**
	 * Returns base path.
	 *
	 * @return string  The base toolkit folder path
	 */
	public function getBasePath()
	{
		return $this->_paths['base'];

	}

	/**
	 * Returns cert path.
	 *
	 * @return string The cert folder path
	 */
	public function getCertPath()
	{
		return $this->_paths['cert'];

	}

	/**
	 * Returns config path.
	 *
	 * @return string The config folder path
	 */
	public function getConfigPath()
	{
		return $this->_paths['config'];

	}

	/**
	 * Returns lib path.
	 *
	 * @return string The library folder path
	 */
	public function getLibPath()
	{
		return $this->_paths['lib'];

	}

	/**
	 * Returns schema path.
	 *
	 * @return string  The external library folder path
	 */
	public function getSchemasPath()
	{
		if ( isset( $this->_paths['schemas'] ) )
		{
			return $this->_paths['schemas'];
		}
		return __DIR__ . '/schemas/';

	}

	/**
	 * Set schemas path
	 *
	 * @param string $path
	 * @return $this
	 */
	public function setSchemasPath( $path )
	{
		$this->_paths['schemas'] = $path;

	}

	/**
	 * Loads settings info from a settings Array
	 *
	 * @param array $settings SAML Toolkit Settings
	 *
	 * @return bool True if the settings info is valid
	 */
	private function _loadSettingsFromArray( array $settings )
	{
		if ( isset( $settings['sp'] ) )
		{
			$this->_sp = $settings['sp'];
		}
		if ( isset( $settings['idp'] ) )
		{
			$this->_idp = $settings['idp'];
		}

		$errors = $this->checkSettings( $settings );
		if ( empty( $errors ) )
		{
			$this->_errors = array();

			if ( isset( $settings['strict'] ) )
			{
				$this->_strict = $settings['strict'];
			}
			if ( isset( $settings['debug'] ) )
			{
				$this->_debug = $settings['debug'];
			}

			if ( isset( $settings['baseurl'] ) )
			{
				$this->_baseurl = $settings['baseurl'];
			}

			if ( isset( $settings['compress'] ) )
			{
				$this->_compress = $settings['compress'];
			}

			if ( isset( $settings['security'] ) )
			{
				$this->_security = $settings['security'];
			}

			if ( isset( $settings['contactPerson'] ) )
			{
				$this->_contacts = $settings['contactPerson'];
			}

			if ( isset( $settings['organization'] ) )
			{
				$this->_organization = $settings['organization'];
			}

			$this->_addDefaultValues();
			return true;
		}
		else
		{
			$this->_errors = $errors;
			return false;
		}

	}

	/**
	 * Loads settings info from the settings file
	 *
	 * @return bool True if the settings info is valid
	 *
	 * @throws Error
	 *
	 * @suppress PhanUndeclaredVariable
	 */
	private function _loadSettingsFromFile()
	{
		$Settings = XSAMLSettings::Get();

		return $this->_loadSettingsFromArray( $Settings );

	}

	/**
	 * Add default values if the settings info is not complete
	 */
	private function _addDefaultValues()
	{
		if ( !isset( $this->_sp['assertionConsumerService']['binding'] ) )
		{
			$this->_sp['assertionConsumerService']['binding'] = Constants::BINDING_HTTP_POST;
		}
		if ( isset( $this->_sp['singleLogoutService'] ) && !isset( $this->_sp['singleLogoutService']['binding'] ) )
		{
			$this->_sp['singleLogoutService']['binding'] = Constants::BINDING_HTTP_REDIRECT;
		}

		if ( !isset( $this->_compress['requests'] ) )
		{
			$this->_compress['requests'] = true;
		}

		if ( !isset( $this->_compress['responses'] ) )
		{
			$this->_compress['responses'] = true;
		}

		// Related to nameID
		if ( !isset( $this->_sp['NameIDFormat'] ) )
		{
			$this->_sp['NameIDFormat'] = Constants::NAMEID_UNSPECIFIED;
		}
		if ( !isset( $this->_security['nameIdEncrypted'] ) )
		{
			$this->_security['nameIdEncrypted'] = false;
		}
		if ( !isset( $this->_security['requestedAuthnContext'] ) )
		{
			$this->_security['requestedAuthnContext'] = true;
		}

		// sign provided
		if ( !isset( $this->_security['authnRequestsSigned'] ) )
		{
			$this->_security['authnRequestsSigned'] = false;
		}
		if ( !isset( $this->_security['logoutRequestSigned'] ) )
		{
			$this->_security['logoutRequestSigned'] = false;
		}
		if ( !isset( $this->_security['logoutResponseSigned'] ) )
		{
			$this->_security['logoutResponseSigned'] = false;
		}
		if ( !isset( $this->_security['signMetadata'] ) )
		{
			$this->_security['signMetadata'] = false;
		}

		// sign expected
		if ( !isset( $this->_security['wantMessagesSigned'] ) )
		{
			$this->_security['wantMessagesSigned'] = false;
		}
		if ( !isset( $this->_security['wantAssertionsSigned'] ) )
		{
			$this->_security['wantAssertionsSigned'] = false;
		}

		// NameID element expected
		if ( !isset( $this->_security['wantNameId'] ) )
		{
			$this->_security['wantNameId'] = true;
		}

		// Relax Destination validation
		if ( !isset( $this->_security['relaxDestinationValidation'] ) )
		{
			$this->_security['relaxDestinationValidation'] = false;
		}

		// Strict Destination match validation
		if ( !isset( $this->_security['destinationStrictlyMatches'] ) )
		{
			$this->_security['destinationStrictlyMatches'] = false;
		}

		// Allow duplicated Attribute Names
		if ( !isset( $this->_security['allowRepeatAttributeName'] ) )
		{
			$this->_security['allowRepeatAttributeName'] = false;
		}

		// InResponseTo
		if ( !isset( $this->_security['rejectUnsolicitedResponsesWithInResponseTo'] ) )
		{
			$this->_security['rejectUnsolicitedResponsesWithInResponseTo'] = false;
		}

		// encrypt expected
		if ( !isset( $this->_security['wantAssertionsEncrypted'] ) )
		{
			$this->_security['wantAssertionsEncrypted'] = false;
		}
		if ( !isset( $this->_security['wantNameIdEncrypted'] ) )
		{
			$this->_security['wantNameIdEncrypted'] = false;
		}

		// XML validation
		if ( !isset( $this->_security['wantXMLValidation'] ) )
		{
			$this->_security['wantXMLValidation'] = true;
		}

		// SignatureAlgorithm
		if ( !isset( $this->_security['signatureAlgorithm'] ) )
		{
			$this->_security['signatureAlgorithm'] = XMLSecurityKey::RSA_SHA256;
		}

		// DigestAlgorithm
		if ( !isset( $this->_security['digestAlgorithm'] ) )
		{
			$this->_security['digestAlgorithm'] = XMLSecurityDSig::SHA256;
		}

		// EncryptionAlgorithm
		if ( !isset( $this->_security['encryption_algorithm'] ) )
		{
			$this->_security['encryption_algorithm'] = XMLSecurityKey::AES128_CBC;
		}

		if ( !isset( $this->_security['lowercaseUrlencoding'] ) )
		{
			$this->_security['lowercaseUrlencoding'] = false;
		}

		// Certificates / Private key /Fingerprint
		if ( !isset( $this->_idp['x509cert'] ) )
		{
			$this->_idp['x509cert'] = '';
		}
		if ( !isset( $this->_idp['certFingerprint'] ) )
		{
			$this->_idp['certFingerprint'] = '';
		}
		if ( !isset( $this->_idp['certFingerprintAlgorithm'] ) )
		{
			$this->_idp['certFingerprintAlgorithm'] = 'sha1';
		}

		if ( !isset( $this->_sp['x509cert'] ) )
		{
			$this->_sp['x509cert'] = '';
		}
		if ( !isset( $this->_sp['privateKey'] ) )
		{
			$this->_sp['privateKey'] = '';
		}

	}

	/**
	 * Checks the settings info.
	 *
	 * @param array $settings Array with settings data
	 *
	 * @return array $errors  Errors found on the settings data
	 */
	public function checkSettings( array $settings )
	{
		if ( empty( $settings ) )
		{
			$errors = array( 'invalid_syntax' );
		}
		else
		{
			$errors = array();
			if ( !$this->_spValidationOnly )
			{
				$idpErrors = $this->checkIdPSettings( $settings );
				$errors = array_merge( $idpErrors, $errors );
			}
			$spErrors = $this->checkSPSettings( $settings );
			$errors = array_merge( $spErrors, $errors );

			$compressErrors = $this->checkCompressionSettings( $settings );
			$errors = array_merge( $compressErrors, $errors );
		}

		return $errors;

	}

	/**
	 * Checks the compression settings info.
	 *
	 * @param array $settings Array with settings data
	 *
	 * @return array $errors  Errors found on the settings data
	 */
	public function checkCompressionSettings( $settings )
	{
		$errors = array();

		if ( isset( $settings['compress'] ) )
		{
			if ( !is_array( $settings['compress'] ) )
			{
				$errors[] = "invalid_syntax";
			}
			else if ( isset( $settings['compress']['requests'] ) && $settings['compress']['requests'] !== true && $settings['compress']['requests'] !== false
			)
			{
				$errors[] = "'compress'=>'requests' values must be true or false.";
			}
			else if ( isset( $settings['compress']['responses'] ) && $settings['compress']['responses'] !== true && $settings['compress']['responses'] !== false
			)
			{
				$errors[] = "'compress'=>'responses' values must be true or false.";
			}
		}
		return $errors;

	}

	/**
	 * Checks the IdP settings info.
	 *
	 * @param array $settings Array with settings data
	 *
	 * @return array $errors  Errors found on the IdP settings data
	 */
	public function checkIdPSettings( array $settings )
	{
		if ( empty( $settings ) )
		{
			return array( 'invalid_syntax' );
		}

		$errors = array();

		if ( !isset( $settings['idp'] ) || empty( $settings['idp'] ) )
		{
			$errors[] = 'idp_not_found';
		}
		else
		{
			$idp = $settings['idp'];
			if ( !isset( $idp['entityId'] ) || empty( $idp['entityId'] ) )
			{
				$errors[] = 'idp_entityId_not_found';
			}

			if ( !isset( $idp['singleSignOnService'] ) || !isset( $idp['singleSignOnService']['url'] ) || empty( $idp['singleSignOnService']['url'] )
			)
			{
				$errors[] = 'idp_sso_not_found';
			}
			else if ( !filter_var( $idp['singleSignOnService']['url'], FILTER_VALIDATE_URL ) )
			{
				$errors[] = 'idp_sso_url_invalid';
			}

			if ( isset( $idp['singleLogoutService'] ) && isset( $idp['singleLogoutService']['url'] ) && !empty( $idp['singleLogoutService']['url'] ) && !filter_var( $idp['singleLogoutService']['url'], FILTER_VALIDATE_URL )
			)
			{
				$errors[] = 'idp_slo_url_invalid';
			}

			if ( isset( $idp['singleLogoutService'] ) && isset( $idp['singleLogoutService']['responseUrl'] ) && !empty( $idp['singleLogoutService']['responseUrl'] ) && !filter_var( $idp['singleLogoutService']['responseUrl'], FILTER_VALIDATE_URL )
			)
			{
				$errors[] = 'idp_slo_response_url_invalid';
			}

			$existsX509 = isset( $idp['x509cert'] ) && !empty( $idp['x509cert'] );
			$existsMultiX509Sign = isset( $idp['x509certMulti'] ) && isset( $idp['x509certMulti']['signing'] ) && !empty( $idp['x509certMulti']['signing'] );
			$existsFingerprint = isset( $idp['certFingerprint'] ) && !empty( $idp['certFingerprint'] );
			if ( !($existsX509 || $existsFingerprint || $existsMultiX509Sign)
			)
			{
				$errors[] = 'idp_cert_or_fingerprint_not_found_and_required';
			}

			if ( isset( $settings['security'] ) )
			{
				$existsMultiX509Enc = isset( $idp['x509certMulti'] ) && isset( $idp['x509certMulti']['encryption'] ) && !empty( $idp['x509certMulti']['encryption'] );

				if ( (isset( $settings['security']['nameIdEncrypted'] ) && $settings['security']['nameIdEncrypted'] == true) && !($existsX509 || $existsMultiX509Enc)
				)
				{
					$errors[] = 'idp_cert_not_found_and_required';
				}
			}
		}

		return $errors;

	}

	/**
	 * Checks the SP settings info.
	 *
	 * @param array $settings Array with settings data
	 *
	 * @return array $errors  Errors found on the SP settings data
	 */
	public function checkSPSettings( array $settings )
	{
		if ( empty( $settings ) )
		{
			return array( 'invalid_syntax' );
		}

		$errors = array();

		if ( !isset( $settings['sp'] ) || empty( $settings['sp'] ) )
		{
			$errors[] = 'sp_not_found';
		}
		else
		{
			$sp = $settings['sp'];
			$security = array();
			if ( isset( $settings['security'] ) )
			{
				$security = $settings['security'];
			}

			if ( !isset( $sp['entityId'] ) || empty( $sp['entityId'] ) )
			{
				$errors[] = 'sp_entityId_not_found';
			}

			if ( !isset( $sp['assertionConsumerService'] ) || !isset( $sp['assertionConsumerService']['url'] ) || empty( $sp['assertionConsumerService']['url'] )
			)
			{
				$errors[] = 'sp_acs_not_found';
			}
			else if ( !filter_var( $sp['assertionConsumerService']['url'], FILTER_VALIDATE_URL ) )
			{
				$errors[] = 'sp_acs_url_invalid';
			}

			if ( isset( $sp['singleLogoutService'] ) && isset( $sp['singleLogoutService']['url'] ) && !filter_var( $sp['singleLogoutService']['url'], FILTER_VALIDATE_URL )
			)
			{
				$errors[] = 'sp_sls_url_invalid';
			}

			if ( isset( $security['signMetadata'] ) && is_array( $security['signMetadata'] ) )
			{
				if ( (!isset( $security['signMetadata']['keyFileName'] ) || !isset( $security['signMetadata']['certFileName'] )) &&
								(!isset( $security['signMetadata']['privateKey'] ) || !isset( $security['signMetadata']['x509cert'] ))
				)
				{
					$errors[] = 'sp_signMetadata_invalid';
				}
			}

			if ( ((isset( $security['authnRequestsSigned'] ) && $security['authnRequestsSigned'] == true) || (isset( $security['logoutRequestSigned'] ) && $security['logoutRequestSigned'] == true) || (isset( $security['logoutResponseSigned'] ) && $security['logoutResponseSigned'] == true) || (isset( $security['wantAssertionsEncrypted'] ) && $security['wantAssertionsEncrypted'] == true) || (isset( $security['wantNameIdEncrypted'] ) && $security['wantNameIdEncrypted'] == true)) && !$this->checkSPCerts()
			)
			{
				$errors[] = 'sp_certs_not_found_and_required';
			}
		}

		if ( isset( $settings['contactPerson'] ) )
		{
			$types = array_keys( $settings['contactPerson'] );
			$validTypes = array( 'technical', 'support', 'administrative', 'billing', 'other' );
			foreach ( $types as $type )
			{
				if ( !in_array( $type, $validTypes ) )
				{
					$errors[] = 'contact_type_invalid';
					break;
				}
			}

			foreach ( $settings['contactPerson'] as $type => $contact )
			{
				if ( !isset( $contact['givenName'] ) || empty( $contact['givenName'] ) || !isset( $contact['emailAddress'] ) || empty( $contact['emailAddress'] )
				)
				{
					$errors[] = 'contact_not_enought_data';
					break;
				}
			}
		}

		if ( isset( $settings['organization'] ) )
		{
			foreach ( $settings['organization'] as $organization )
			{
				if ( !isset( $organization['name'] ) || empty( $organization['name'] ) || !isset( $organization['displayname'] ) || empty( $organization['displayname'] ) || !isset( $organization['url'] ) || empty( $organization['url'] )
				)
				{
					$errors[] = 'organization_not_enought_data';
					break;
				}
			}
		}

		return $errors;

	}

	/**
	 * Checks if the x509 certs of the SP exists and are valid.
	 *
	 * @return bool
	 */
	public function checkSPCerts()
	{
		$key = $this->getSPkey();
		$cert = $this->getSPcert();
		return (!empty( $key ) && !empty( $cert ));

	}

	/**
	 * Returns the x509 private key of the SP.
	 *
	 * @return string SP private key
	 */
	public function getSPkey()
	{
		$key = null;
		if ( isset( $this->_sp['privateKey'] ) && !empty( $this->_sp['privateKey'] ) )
		{
			$key = $this->_sp['privateKey'];
		}
		return $key;

	}

	/**
	 * Returns the x509 public cert of the SP.
	 *
	 * @return string SP public cert
	 */
	public function getSPcert()
	{
		$cert = null;

		if ( isset( $this->_sp['x509cert'] ) && !empty( $this->_sp['x509cert'] ) )
		{
			$cert = $this->_sp['x509cert'];
		}
		return $cert;

	}

	/**
	 * Returns the x509 public of the SP that is
	 * planed to be used soon instead the other
	 * public cert
	 *
	 * @return string SP public cert New
	 */
	public function getSPcertNew()
	{
		$cert = null;

		if ( isset( $this->_sp['x509certNew'] ) && !empty( $this->_sp['x509certNew'] ) )
		{
			$cert = $this->_sp['x509certNew'];
		}
		else
		{
			
		}
		return $cert;

	}

	/**
	 * Gets the IdP data.
	 *
	 * @return array  IdP info
	 */
	public function getIdPData()
	{
		return $this->_idp;

	}

	/**
	 * Gets the SP data.
	 *
	 * @return array  SP info
	 */
	public function getSPData()
	{
		return $this->_sp;

	}

	/**
	 * Gets security data.
	 *
	 * @return array  SP info
	 */
	public function getSecurityData()
	{
		return $this->_security;

	}

	/**
	 * Gets contact data.
	 *
	 * @return array  SP info
	 */
	public function getContacts()
	{
		return $this->_contacts;

	}

	/**
	 * Gets organization data.
	 *
	 * @return array  SP info
	 */
	public function getOrganization()
	{
		return $this->_organization;

	}

	/**
	 * Should SAML requests be compressed?
	 *
	 * @return bool Yes/No as True/False
	 */
	public function shouldCompressRequests()
	{
		return $this->_compress['requests'];

	}

	/**
	 * Should SAML responses be compressed?
	 *
	 * @return bool Yes/No as True/False
	 */
	public function shouldCompressResponses()
	{
		return $this->_compress['responses'];

	}

	/**
	 * Gets the IdP SSO url.
	 *
	 * @return string|null The url of the IdP Single Sign On Service
	 */
	public function getIdPSSOUrl()
	{
		$ssoUrl = null;
		if ( isset( $this->_idp['singleSignOnService'] ) && isset( $this->_idp['singleSignOnService']['url'] ) )
		{
			$ssoUrl = $this->_idp['singleSignOnService']['url'];
		}
		return $ssoUrl;

	}

	/**
	 * Gets the IdP SLO url.
	 *
	 * @return string|null The request url of the IdP Single Logout Service
	 */
	public function getIdPSLOUrl()
	{
		$sloUrl = null;
		if ( isset( $this->_idp['singleLogoutService'] ) && isset( $this->_idp['singleLogoutService']['url'] ) )
		{
			$sloUrl = $this->_idp['singleLogoutService']['url'];
		}
		return $sloUrl;

	}

	/**
	 * Gets the IdP SLO response url.
	 *
	 * @return string|null The response url of the IdP Single Logout Service
	 */
	public function getIdPSLOResponseUrl()
	{
		if ( isset( $this->_idp['singleLogoutService'] ) && isset( $this->_idp['singleLogoutService']['responseUrl'] ) )
		{
			return $this->_idp['singleLogoutService']['responseUrl'];
		}
		return $this->getIdPSLOUrl();

	}

	/**
	 * Gets the SP metadata. The XML representation.
	 *
	 * @param bool $alwaysPublishEncryptionCert When 'true', the returned
	 * metadata will always include an 'encryption' KeyDescriptor. Otherwise,
	 * the 'encryption' KeyDescriptor will only be included if
	 * $advancedSettings['security']['wantNameIdEncrypted'] or
	 * $advancedSettings['security']['wantAssertionsEncrypted'] are enabled.
	 * @param int|null      $validUntil    Metadata's valid time
	 * @param int|null      $cacheDuration Duration of the cache in seconds
	 *
	 * @return string  SP metadata (xml)
	 * @throws Exception
	 * @throws Error
	 */
	public function getSPMetadata( $alwaysPublishEncryptionCert = false, $validUntil = null, $cacheDuration = null )
	{
		$metadata = Metadata::builder( $this->_sp, $this->_security['authnRequestsSigned'], $this->_security['wantAssertionsSigned'], $validUntil, $cacheDuration, $this->getContacts(), $this->getOrganization() );
		$certNew = $this->getSPcertNew();
		if ( !empty( $certNew ) )
		{
			$metadata = Metadata::addX509KeyDescriptors(
											$metadata,
											$certNew,
											$alwaysPublishEncryptionCert || $this->_security['wantNameIdEncrypted'] || $this->_security['wantAssertionsEncrypted']
			);
		}

		$cert = $this->getSPcert();
		if ( !empty( $cert ) )
		{
			$metadata = Metadata::addX509KeyDescriptors(
											$metadata,
											$cert,
											$alwaysPublishEncryptionCert || $this->_security['wantNameIdEncrypted'] || $this->_security['wantAssertionsEncrypted']
			);
		}

		//Sign Metadata
		if ( isset( $this->_security['signMetadata'] ) && $this->_security['signMetadata'] != false )
		{
			if ( $this->_security['signMetadata'] === true )
			{
				$keyMetadata = $this->getSPkey();
				$certMetadata = $cert;

				if ( !$keyMetadata )
				{
					throw new XSAMLError(
													'SP Private key not found.',
													XSAMLError::PRIVATE_KEY_FILE_NOT_FOUND
					);
				}

				if ( !$certMetadata )
				{
					throw new XSAMLError(
													'SP Public cert not found.',
													XSAMLError::PUBLIC_CERT_FILE_NOT_FOUND
					);
				}
			}
			else if ( isset( $this->_security['signMetadata']['keyFileName'] ) &&
							isset( $this->_security['signMetadata']['certFileName'] ) )
			{
				$keyFileName = $this->_security['signMetadata']['keyFileName'];
				$certFileName = $this->_security['signMetadata']['certFileName'];

				$keyMetadataFile = $this->_paths['cert'] . $keyFileName;
				$certMetadataFile = $this->_paths['cert'] . $certFileName;

				if ( !file_exists( $keyMetadataFile ) )
				{
					throw new XSAMLError(
													'SP Private key file not found: %s',
													XSAMLError::PRIVATE_KEY_FILE_NOT_FOUND,
													array( $keyMetadataFile )
					);
				}

				if ( !file_exists( $certMetadataFile ) )
				{
					throw new XSAMLError(
													'SP Public cert file not found: %s',
													XSAMLError::PUBLIC_CERT_FILE_NOT_FOUND,
													array( $certMetadataFile )
					);
				}
				$keyMetadata = file_get_contents( $keyMetadataFile );
				$certMetadata = file_get_contents( $certMetadataFile );
			}
			else if ( isset( $this->_security['signMetadata']['privateKey'] ) &&
							isset( $this->_security['signMetadata']['x509cert'] ) )
			{
				$keyMetadata = Utils::formatPrivateKey( $this->_security['signMetadata']['privateKey'] );
				$certMetadata = Utils::formatCert( $this->_security['signMetadata']['x509cert'] );
				if ( !$keyMetadata )
				{
					throw new XSAMLError(
													'Private key not found.',
													XSAMLError::PRIVATE_KEY_FILE_NOT_FOUND
					);
				}

				if ( !$certMetadata )
				{
					throw new XSAMLError(
													'Public cert not found.',
													XSAMLError::PUBLIC_CERT_FILE_NOT_FOUND
					);
				}
			}
			else
			{
				throw new XSAMLError(
												'Invalid Setting: signMetadata value of the sp is not valid',
												XSAMLError::SETTINGS_INVALID_SYNTAX
				);
			}

			$signatureAlgorithm = $this->_security['signatureAlgorithm'];
			$digestAlgorithm = $this->_security['digestAlgorithm'];
			$metadata = Metadata::signMetadata( $metadata, $keyMetadata, $certMetadata, $signatureAlgorithm, $digestAlgorithm );
		}
		return $metadata;

	}

	/**
	 * Validates an XML SP Metadata.
	 *
	 * @param string $xml Metadata's XML that will be validate
	 *
	 * @return array The list of found errors
	 *
	 * @throws Exception
	 */
	public function validateMetadata( $xml )
	{
		assert( is_string( $xml ) );

		$errors = array();
		$res = Utils::validateXML( $xml, 'saml-schema-metadata-2.0.xsd', $this->_debug, $this->getSchemasPath() );
		if ( !$res instanceof DOMDocument )
		{
			$errors[] = $res;
		}
		else
		{
			$dom = $res;
			$element = $dom->documentElement;
			if ( $element->tagName !== 'md:EntityDescriptor' )
			{
				$errors[] = 'noEntityDescriptor_xml';
			}
			else
			{
				$validUntil = $cacheDuration = $expireTime = null;

				if ( $element->hasAttribute( 'validUntil' ) )
				{
					$validUntil = Utils::parseSAML2Time( $element->getAttribute( 'validUntil' ) );
				}
				if ( $element->hasAttribute( 'cacheDuration' ) )
				{
					$cacheDuration = $element->getAttribute( 'cacheDuration' );
				}

				$expireTime = Utils::getExpireTime( $cacheDuration, $validUntil );
				if ( isset( $expireTime ) && time() > $expireTime )
				{
					$errors[] = 'expired_xml';
				}
			}
		}

		// TODO: Support Metadata Sign Validation

		return $errors;

	}

	/**
	 * Formats the IdP cert.
	 */
	public function formatIdPCert()
	{
		if ( isset( $this->_idp['x509cert'] ) )
		{
			$this->_idp['x509cert'] = Utils::formatCert( $this->_idp['x509cert'] );
		}

	}

	/**
	 * Formats the Multple IdP certs.
	 */
	public function formatIdPCertMulti()
	{
		if ( isset( $this->_idp['x509certMulti'] ) )
		{
			if ( isset( $this->_idp['x509certMulti']['signing'] ) )
			{
				foreach ( $this->_idp['x509certMulti']['signing'] as $i => $cert )
				{
					$this->_idp['x509certMulti']['signing'][$i] = Utils::formatCert( $cert );
				}
			}
			if ( isset( $this->_idp['x509certMulti']['encryption'] ) )
			{
				foreach ( $this->_idp['x509certMulti']['encryption'] as $i => $cert )
				{
					$this->_idp['x509certMulti']['encryption'][$i] = Utils::formatCert( $cert );
				}
			}
		}

	}

	/**
	 * Formats the SP cert.
	 */
	public function formatSPCert()
	{
		if ( isset( $this->_sp['x509cert'] ) )
		{
			$this->_sp['x509cert'] = Utils::formatCert( $this->_sp['x509cert'] );
		}

	}

	/**
	 * Formats the SP cert.
	 */
	public function formatSPCertNew()
	{
		if ( isset( $this->_sp['x509certNew'] ) )
		{
			$this->_sp['x509certNew'] = Utils::formatCert( $this->_sp['x509certNew'] );
		}

	}

	/**
	 * Formats the SP private key.
	 */
	public function formatSPKey()
	{
		if ( isset( $this->_sp['privateKey'] ) )
		{
			$this->_sp['privateKey'] = Utils::formatPrivateKey( $this->_sp['privateKey'] );
		}

	}

	/**
	 * Returns an array with the errors, the array is empty when the settings is ok.
	 *
	 * @return array Errors
	 */
	public function getErrors()
	{
		return $this->_errors;

	}

	/**
	 * Activates or deactivates the strict mode.
	 *
	 * @param bool $value Strict parameter
	 *
	 * @throws Exception
	 */
	public function setStrict( $value )
	{
		if ( !is_bool( $value ) )
		{
			throw new Exception( 'Invalid value passed to setStrict()' );
		}

		$this->_strict = $value;

	}

	/**
	 * Returns if the 'strict' mode is active.
	 *
	 * @return bool Strict parameter
	 */
	public function isStrict()
	{
		return $this->_strict;

	}

	/**
	 * Returns if the debug is active.
	 *
	 * @return bool Debug parameter
	 */
	public function isDebugActive()
	{
		return $this->_debug;

	}

	/**
	 * Sets the IdP certificate.
	 *
	 * @param string $cert IdP certificate
	 */
	public function setIdPCert( $cert )
	{
		$this->_idp['x509cert'] = $cert;
		$this->formatIdPCert();

	}

	public static function Get()
	{
		$URI = URI::getInstance();
		$BaseURL = $URI->toString( array( 'scheme', 'user', 'pass', 'host', 'port' ) );
		$Service = Helper::getConfig( 'azure_saml_sso_service' );
		$EntityID = Helper::getConfig( 'azure_saml_entity_id' );
		$Logout = Helper::getConfig( 'azure_saml_sso_logout_service' );
		$Certificate = Helper::getConfig( 'azure_saml_sso_x509cert' );
		$Settings = array(
				'sp' => array(
						'entityId' => $BaseURL . '/api/SAML/MetaData',
						'assertionConsumerService' => array(
								'url' => $BaseURL . '/api/SAML/ACS',
						),
						'singleLogoutService' => array(
								'url' => $BaseURL . '/api/SAML/SLO',
						),
						'NameIDFormat' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified',
				),
				'idp' => array(
						'entityId' => $EntityID,
						'singleSignOnService' => array(
								'url' => $Service,
						),
						'singleLogoutService' => array(
								'url' => $Logout,
						),
						'x509cert' => $Certificate,
				),
		);
		return $Settings;

	}

	public static function GetAdvancedSettings()
	{
		$AdvancedSettings = array(
				// Compression settings
				// Handle if the getRequest/getResponse methods will return the Request/Response deflated.
				// But if we provide a $deflate boolean parameter to the getRequest or getResponse
				// method it will have priority over the compression settings.
				'compress' => array(
						'requests' => true,
						'responses' => true
				),
				// Security settings
				'security' => array(
						/** signatures and encryptions offered */
						// Indicates that the nameID of the <samlp:logoutRequest> sent by this SP
						// will be encrypted.
						'nameIdEncrypted' => false,
						// Indicates whether the <samlp:AuthnRequest> messages sent by this SP
						// will be signed.              [The Metadata of the SP will offer this info]
						'authnRequestsSigned' => false,
						// Indicates whether the <samlp:logoutRequest> messages sent by this SP
						// will be signed.
						'logoutRequestSigned' => false,
						// Indicates whether the <samlp:logoutResponse> messages sent by this SP
						// will be signed.
						'logoutResponseSigned' => false,
						/* Sign the Metadata
						  False || True (use sp certs) || array (
						  'keyFileName' => 'metadata.key',
						  'certFileName' => 'metadata.crt'
						  )
						  || array (
						  'x509cert' => '',
						  'privateKey' => ''
						  )
						 */
						'signMetadata' => false,
						/** signatures and encryptions required * */
						// Indicates a requirement for the <samlp:Response>, <samlp:LogoutRequest> and
						// <samlp:LogoutResponse> elements received by this SP to be signed.
						'wantMessagesSigned' => false,
						// Indicates a requirement for the <saml:Assertion> elements received by
						// this SP to be encrypted.
						'wantAssertionsEncrypted' => false,
						// Indicates a requirement for the <saml:Assertion> elements received by
						// this SP to be signed.        [The Metadata of the SP will offer this info]
						'wantAssertionsSigned' => false,
						// Indicates a requirement for the NameID element on the SAMLResponse received
						// by this SP to be present.
						'wantNameId' => true,
						// Indicates a requirement for the NameID received by
						// this SP to be encrypted.
						'wantNameIdEncrypted' => false,
						// Authentication context.
						// Set to false and no AuthContext will be sent in the AuthNRequest,
						// Set true or don't present this parameter and you will get an AuthContext 'exact' 'urn:oasis:names:tc:SAML:2.0:ac:classes:PasswordProtectedTransport'
						// Set an array with the possible auth context values: array('urn:oasis:names:tc:SAML:2.0:ac:classes:Password', 'urn:oasis:names:tc:SAML:2.0:ac:classes:X509'),
						'requestedAuthnContext' => false,
						// Allows the authn comparison parameter to be set, defaults to 'exact' if
						// the setting is not present.
						'requestedAuthnContextComparison' => 'exact',
						// Indicates if the SP will validate all received xmls.
						// (In order to validate the xml, 'strict' and 'wantXMLValidation' must be true).
						'wantXMLValidation' => true,
						// If true, SAMLResponses with an empty value at its Destination
						// attribute will not be rejected for this fact.
						'relaxDestinationValidation' => false,
						// If true, Destination URL should strictly match to the address to
						// which the response has been sent.
						// Notice that if 'relaxDestinationValidation' is true an empty Destintation
						// will be accepted.
						'destinationStrictlyMatches' => false,
						// If true, the toolkit will not raised an error when the Statement Element
						// contain atribute elements with name duplicated
						'allowRepeatAttributeName' => false,
						// If true, SAMLResponses with an InResponseTo value will be rejectd if not
						// AuthNRequest ID provided to the validation method.
						'rejectUnsolicitedResponsesWithInResponseTo' => false,
						// Algorithm that the toolkit will use on signing process. Options:
						//    'http://www.w3.org/2000/09/xmldsig#rsa-sha1'
						//    'http://www.w3.org/2000/09/xmldsig#dsa-sha1'
						//    'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256'
						//    'http://www.w3.org/2001/04/xmldsig-more#rsa-sha384'
						//    'http://www.w3.org/2001/04/xmldsig-more#rsa-sha512'
						// Notice that rsa-sha1 is a deprecated algorithm and should not be used
						'signatureAlgorithm' => 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256',
						// Algorithm that the toolkit will use on digest process. Options:
						//    'http://www.w3.org/2000/09/xmldsig#sha1'
						//    'http://www.w3.org/2001/04/xmlenc#sha256'
						//    'http://www.w3.org/2001/04/xmldsig-more#sha384'
						//    'http://www.w3.org/2001/04/xmlenc#sha512'
						// Notice that sha1 is a deprecated algorithm and should not be used
						'digestAlgorithm' => 'http://www.w3.org/2001/04/xmlenc#sha256',
						// Algorithm that the toolkit will use for encryption process. Options:
						// 'http://www.w3.org/2001/04/xmlenc#tripledes-cbc'
						// 'http://www.w3.org/2001/04/xmlenc#aes128-cbc'
						// 'http://www.w3.org/2001/04/xmlenc#aes192-cbc'
						// 'http://www.w3.org/2001/04/xmlenc#aes256-cbc'
						// 'http://www.w3.org/2009/xmlenc11#aes128-gcm'
						// 'http://www.w3.org/2009/xmlenc11#aes192-gcm'
						// 'http://www.w3.org/2009/xmlenc11#aes256-gcm';
						// Notice that aes-cbc are not consider secure anymore so should not be used
						'encryption_algorithm' => 'http://www.w3.org/2001/04/xmlenc#aes128-cbc',
						// ADFS URL-Encodes SAML data as lowercase, and the toolkit by default uses
						// uppercase. Turn it True for ADFS compatibility on signature verification
						'lowercaseUrlencoding' => false,
				),
				// Contact information template, it is recommended to suply a technical and support contacts
				'contactPerson' => array(
						'technical' => array(
								'givenName' => '',
								'emailAddress' => ''
						),
						'support' => array(
								'givenName' => '',
								'emailAddress' => ''
						),
				),
				// Organization information template, the info in en_US lang is recomended, add more if required
				'organization' => array(
						'en-US' => array(
								'name' => '',
								'displayname' => '',
								'url' => ''
						),
				),
		);
		return $AdvancedSettings;

	}

	public function getBaseURL()
	{
		return URI::root();

	}

	public function getValue( $Key )
	{
		return C::_( $Key, $this->Get() );

	}

}
