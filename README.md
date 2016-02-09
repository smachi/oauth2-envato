# Envato Provider for PHP League OAuth 2.0 Client


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
    $owner          = $provider->getResourceOwner( $token, 'username' );
    $ownerEmail     = $provider->getResourceOwner( $token, 'email' );
    $ownerPurchases = $provider->getResourceOwner( $token, 'purchases', [ 'filter_by' => 'wordpress-themes' ] );

    $username   = preg_replace( '/[^a-z0-9-_]/i', '', $owner->getUsername() );
    $email      = $ownerEmail->getEmail();
    $purchases  = $ownerPurchases->getPurchases();
    $authorName = 'YourEnvatoAuthorUserName';

    if ( empty( $purchases ) ) {
        throw new \Exception(
            "Only current buyers have access to <strong>$authorName</strong> support forums.",
            401
        );
    }
    else{

        // Check for item support validity
        $maybePurchaseFromAuthor = FALSE;

        foreach($purchases as $item){

            if ( $authorName == $item['item']['author_username'] ){

                $maybePurchaseFromAuthor = TRUE;
                if ( strtotime( $item['supported_until'] ) > time() ){
                    // The support license is still valid
                    $itemUrl = $item['item']['url'];
                    break;
                }

            }

        }

        // Support expired
        if ( $maybePurchaseFromAuthor ){

            if ( ! $itemUrl ) {
                throw new \Exception(
                    'Your support license has expired.<br>Please <a href="' . $itemUrl . '" target="_blank">renew it</a> and come back again to get access.',
                    901
                );
            }

        }
        // Did not purchase any item from author
        else{
            throw new \Exception(
                "Only current buyers have access to <strong>$authorName</strong> support forums.",
                401
            );
        }


    }

} catch (\Exception $e){
	die( $e->getMessage() );
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


### $provider->getResourceOwner( $token, 'purchases', [ 'filter_by' => 'NULL | wordpress-themes | wordpress-plugins' ] )

Object:

```
$user->getPurchases()

$user->getPurchasesCount()

```

