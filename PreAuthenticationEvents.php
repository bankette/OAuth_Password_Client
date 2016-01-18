<?php
namespace Jumaq\Bundle\OAuthClientBundle;


final class PreAuthenticationEvents
{
/**
* PostAuthenticationEvent is fired just before authentication
*
* The listener receive the authentication event :
* Event/OAuth2AutenticationEvent
*
* @var string
*/
const OAUTH2_PRE_AUTHENTICATION = 'oauth2.pre_authentication';
}