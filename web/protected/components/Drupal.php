<?php
/**
 * @file
 * @copyright      Copyright (c) 2009-2011 DreamFactory Software, Inc.
 * @author         Jerry Ablan <jablan@dreamfactory.com>
 */
/**
 * DrupalAuthenticator
 */
class Drupal
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string
	 */
	const Endpoint = 'http://cerberus.fabric.dreamfactory.com/api/drupal';

	//*************************************************************************
	//* Methods
	//*************************************************************************

	/**
	 * @param string $url
	 * @param array  $payload
	 * @param array  $options
	 * @param string $method
	 *
	 * @return \stdClass|string
	 */
	protected static function _drupal( $url, array $payload = array(), array $options = array(), $method = Curl::Post )
	{
		$_url = '/' . ltrim( $url, '/' );

		if ( empty( $options ) )
		{
			$options = array();
		}

		if ( !isset( $options[CURLOPT_HTTPHEADER] ) )
		{
			$options[CURLOPT_HTTPHEADER] = array( 'Content-Type: application/json' );
		}
		else
		{
			$options[CURLOPT_HTTPHEADER][] = 'Content-Type: application/json';
		}

		//	Add in a source block
		$payload['source'] = array(
			'host'    => gethostname(),
			'address' => gethostbynamel( gethostname() ),
		);

		$payload['dsp-auth-key'] = md5( microtime( true ) );

//		$payload = json_encode( $payload );

		return Curl::request( $method, static::Endpoint . $_url, json_encode( $payload ), $options );
	}

	/**
	 * @param string $userName
	 * @param string $password
	 *
	 * @return bool
	 */
	public static function authenticateUser( $userName, $password )
	{
		$_payload = array(
			'email'    => $userName,
			'password' => $password,
		);

		if ( false !== ( $_response = static::_drupal( 'drupalValidate', $_payload ) ) )
		{
			if ( $_response->success )
			{
				return $_response->details;
			}
		}

		return false;
	}

	/**
	 * @param int $userId
	 *
	 * @return stdClass|string
	 */
	public static function getUser( $userId )
	{
		$_payload = array(
			'id' => $userId,
		);

		$_response = static::_drupal( 'drupalUser', $_payload );

		return $_response->details;
	}
}