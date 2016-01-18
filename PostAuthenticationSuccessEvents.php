<?php
namespace Oxygem\Bundle\OAuthClientBundle;


final class PostAuthenticationSuccessEvents
{
/**
* PostAuthenticationEvent is fired after a successful authentication
*
* The listener receive the authentication event :
* Event/OAuth2AutenticationEvent
*
* @var string
*/
const OAUTH2_POST_AUTHENTICATION_SUCCESS = 'oxygem.oauth2.post_authentication.success';
}