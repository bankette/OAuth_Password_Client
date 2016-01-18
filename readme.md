# Installation

Add the bundle to your composer.json
``` /composer.json
#/composer.json
    "require": {
        [...]
        "jumaq/oauth-client-bundle": "dev-master"
```

# Configuration

Create a new firewall that will use the oauth2 password credential
``` /app/config/security.yml
#/app/config/security.yml
security:
    firewalls:
        main:
            pattern:   ^/
            anonymous:    true
            remoteapi_oauth: true
```
Insert the required informations in your parameters.yml :
```
parameters:
    remote_api: 'http://my_remote_api'
    remote_api.id: : my_api_id
    remote_api.secret: my_api_secret
```

Optional informations that could be modified :
```
#/app/config/config.yml or in parameters.yml
parameters:
    oauth_client.login_success_route: custom_route_name_after_login_success
    oauth_client.login_success_template: AcmeBundle:Security:custom_view_after_login_success.html.twig
    oauth_client.login_route: custom_login_route_name_after_login_success # default fos_user_security_login
```

# Events

6 events have been created :
- Pre-authentication : oauth2.pre_authentication
- Post-authentication-success : oauth2.post_authentication.success
- Post-authentication-failure : oauth2.post_authentication.failure
- Pre-refresh-token : oauth2.pre_refresh
- Post-refresh-token-success : oauth2.post_refresh.success
- Post-refresh-token-failure : oauth2.post_refresh.failure

To use them :
```
namespace AcmeBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Jumaq\Bundle\OAuthClientBundle\Event\OAuth2AuthenticationEvent;
use Jumaq\Bundle\OAuthClientBundle\PostAuthenticationFailureEvents;
use Jumaq\Bundle\OAuthClientBundle\PostAuthenticationSuccessEvents;
use Jumaq\Bundle\OAuthClientBundle\PreAuthenticationEvents;
use Jumaq\Bundle\OAuthClientBundle\PreRefreshEvents;
use Jumaq\Bundle\OAuthClientBundle\PostRefreshFailureEvents;
use Jumaq\Bundle\OAuthClientBundle\PostRefreshSuccessEvents;

class OAuth2Subscriber implements EventSubscriberInterface
{
    static public function getSubscribedEvents()
    {
        return array(
            PreAuthenticationEvents::OAUTH2_PRE_AUTHENTICATION => array('onPreAuth'),
            PostAuthenticationSuccessEvents::OAUTH2_POST_AUTHENTICATION_SUCCESS => array('onPostAuthSuccess'),
            PostAuthenticationFailureEvents::OAUTH2_POST_AUTHENTICATION_FAILURE => array('onPostAuthFailure'),
            PreRefreshEvents::OAUTH2_PRE_REFRESH => array('onPreRefresh'),
            PostRefreshSuccessEvents::OAUTH2_POST_REFRESH_SUCCESS => array('onPostRefreshSuccess'),
            PostRefreshFailureEvents::OAUTH2_POST_REFRESH_FAILURE => array('onPostRefreshFailure'),
        );
    }

    public function onPreAuth(OAuth2AuthenticationEvent $event)
    {
// ...
    }

    public function onPostAuthSuccess(OAuth2AuthenticationEvent $event)
    {
// ...
    }

    public function onPostAuthFailure(OAuth2AuthenticationEvent $event)
    {
// ...
    }

    public function onPreRefresh(OAuth2AuthenticationEvent $event)
    {
// ...
    }

    public function onPostRefreshSuccess(OAuth2AuthenticationEvent $event)
    {
// ...
    }

    public function onPostRefreshFailure(OAuth2AuthenticationEvent $event)
    {
// ...
    }

}
```
And declare the service :
```
services:
    my_bundle.oauth2_listener:
        class: AcmeBundle\EventListener\OAuth2Subscriber
        tags:
            - { name: kernel.event_subscriber }
```