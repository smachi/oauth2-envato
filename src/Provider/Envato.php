<?php

namespace Smachi\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\EnvatoUser;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

class Envato extends AbstractProvider {

	use BearerAuthorizationTrait;

	/**
	 * Api domain
	 *
	 * @var string
	 */
	public $apiDomain = 'https://api.envato.com';

	/**
	 * Get authorization url to begin OAuth flow
	 *
	 * @return string
	 */
	public function getBaseAuthorizationUrl() {
		return "$this->apiDomain/authorization";
	}

	/**
	 * Get access token url to retrieve token
	 *
	 * @param  array $params
	 *
	 * @return string
	 */
	public function getBaseAccessTokenUrl( array $params ) {
		return "$this->apiDomain/token";
	}

	/**
	 * Get provider url to fetch user details
	 *
	 * @param  AccessToken $token
	 *
	 * @return string
	 */
	public function getResourceOwnerDetailsUrl( AccessToken $token ) {
		return "$this->apiDomain/v1/market/user";
	}

	/**
	 * Get the default scopes used by this provider.
	 *
	 * This should not be a complete list of all scopes, but the minimum
	 * required for the provider user interface!
	 *
	 * @return array
	 */
	protected function getDefaultScopes() {
		return [ ];
	}

	/**
	 * Check a provider response for errors.
	 *
	 * @link   https://developer.github.com/v3/#client-errors
	 * @throws IdentityProviderException
	 *
	 * @param  ResponseInterface $response
	 * @param  string            $data Parsed response data
	 *
	 * @return void
	 */
	protected function checkResponse( ResponseInterface $response, $data ) {
		if ( $response->getStatusCode() >= 400 ) {
			throw new IdentityProviderException(
				$data['message'] ?: $response->getReasonPhrase(),
				$response->getStatusCode(),
				$response
			);
		}
	}

	/**
	 * Generate a user object from a successful user details request.
	 *
	 * @param array       $response
	 * @param AccessToken $token
	 *
	 * @return League\OAuth2\Client\Provider\ResourceOwnerInterface
	 */
	protected function createResourceOwner( array $response, AccessToken $token ) {
		$user = new EnvatoUser( $response );

		return $user->setDomain( $this->apiDomain );
	}
}
