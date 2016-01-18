<?php
namespace Jumaq\Bundle\OAuthClientBundle;


final class PostRefreshSuccessEvents
{
/**
* PostAuthenticationEvent is fired after a successful authentication
*
* The listener receive the authentication event :
* Event/OAuth2AutenticationEvent
*
* @var string
*/
const OAUTH2_POST_REFRESH_SUCCESS = 'oauth2.post_refresh.success';
}