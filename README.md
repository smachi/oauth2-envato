# Envato Provider for OAuth 2.0 Client


## Installation

To install, use composer:

``` composer require smachi/envato-oauth2-provider ```


## Authorization Code Flow
```
$provider = new \Smachi\OAuth2\Client\Provider\Envato([
    'clientId'          => '{envato-client-id}',
    'clientSecret'      => '{envato-client-secret}',
    'redirectUri'       => 'https://example.com/callback-url',
]);

if ( ! isset( $_GET['code'] ) ) {

	// If we don't have an authorization code then get one
	$authUrl = $provider->getAuthorizationUrl();
	$_SESSION['oauth2state'] = $provider->getState();

	return new RedirectResponse( $authUrl );

}
// Check given state against previously stored one to mitigate CSRF attack
elseif ( empty( $_GET['state'] ) || ( $_GET['state'] !== $_SESSION['oauth2state'] ) ) {
	unset( $_SESSION['oauth2state'] );
	exit('Invalid state');
}

// Try to get an access token (using the authorization code grant)
$token = $provider->getAccessToken( 'authorization_code', [
	'code' => $_GET['code']
] );

try {

	// We got an access token, let's now get the user's details
	$owner           = $provider->getResourceOwner( $token, 'username' );
	$owner_email     = $provider->getResourceOwner( $token, 'email' );
	$owner_purchases = $provider->getResourceOwner( $token, 'purchases' );

	$username    = preg_replace( '/[^a-z0-9-_]/i', '', $owner->getUsername() );
	$email       = $owner_email->getEmail();
	$purchases   = $owner_purchases->getPurchases();
	$author_data = $owner_purchases->getEnvatoAuthor();

	if ( empty( $purchases ) ) {
		throw new \Exception(
			"No purchases made by $username from {$author_data['username']}",
			401
		);
	}

} catch (\Exception $e){
	die( $e->getMessage() ;)
}
```


## Some Auth User Data

### $provider->getResourceOwner( $token, 'username' )

Object:

```
$user->getUsername()

```

### $provider->getResourceOwner( $token, 'email' )

Object:

```
$user->getEmail()

```


### $provider->getResourceOwner( $token, 'purchases' )

Object:

```
$user->getPurchases()

$user->getEnvatoAuthor()

```

