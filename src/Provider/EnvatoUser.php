<?php
namespace Smachi\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class EnvatoUser implements ResourceOwnerInterface {

	/**
	 * Domain
	 *
	 * @var string
	 */
	protected $domain;

	/**
	 * Raw response
	 *
	 * @var array
	 */
	protected $response;

	/**
	 * Creates new resource owner.
	 *
	 * @param array $response
	 */
	public function __construct( array $response = array() ) {
		$this->response = $response;
	}

	/**
	 * Get resource owner id
	 *
	 * @return string|null
	 */
	public function getId() {
		return $this->response['id'] ?: NULL;
	}

	/**
	 * Get resource owner email
	 *
	 * @return string|null
	 */
	public function getEmail() {
		return $this->response['email'] ?: NULL;
	}


	/**
	 * Get resource owner username
	 *
	 * @return string|null
	 */
	public function getUsername() {
		return $this->response['username'] ?: NULL;
	}

	/**
	 * Get resource owner purchases array
	 *
	 * @return array
	 */
	public function getPurchases() {
		return $this->response['purchases'] ?: [];
	}

	/**
	 * Get the Envato author data
	 *
	 * @return array
	 */
	public function getEnvatoAuthor() {
		return $this->response['author'] ?: [];
	}

	/**
	 * Set resource owner domain
	 *
	 * @param  string $domain
	 *
	 * @return ResourceOwner
	 */
	public function setDomain( $domain ) {
		$this->domain = $domain;

		return $this;
	}

	/**
	 * Return all of the owner details available as an array.
	 *
	 * @return array
	 */
	public function toArray() {
		return $this->response;
	}
}
