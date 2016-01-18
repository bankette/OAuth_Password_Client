<?php
namespace Oxygem\Bundle\OAuthClientBundle;


final class PostAuthenticationFailureEvents
{
/**
* PostAuthenticationEvent is fired after a failed authentication
*
* The listener receive the authentication event :
* Event/OAuth2AutenticationEvent
*
* @var string
*/
const OAUTH2_POST_AUTHENTICATION_FAILURE = 'oxygem.oauth2.post_authentication.failure';
}