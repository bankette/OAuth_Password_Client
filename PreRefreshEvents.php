<?php
namespace Oxygem\Bundle\OAuthClientBundle;


final class PreRefreshEvents
{
/**
* PostAuthenticationEvent is fired just before authentication
*
* The listener receive the authentication event :
* Event/OAuth2AutenticationEvent
*
* @var string
*/
const OAUTH2_PRE_REFRESH = 'oxygem.oauth2.pre_refresh';
}