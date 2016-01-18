# Installation

Add the bundle to your composer.json
``` /composer.json
#/composer.json
    "require": {
        [...]
        "oxygem/oauth-client-bundle": "dev-master"
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
            dealfeedapi_oauth: true
```
Insert the required informations in your parameters.yml :
```
parameters:
    apiradins: 'http://dealfeed-api.dev.odiso.com'
    dealfeed_api.id: 6_5t1pogw5tio8sowwoo0wcs0008s0ws8c0kok00kwskw80okok4
    dealfeed_api.secret: 104d7mttyx68s8080ookwgkow00go44w4skossco0ko0sg0k0c
```

Optional informations that could be modified :
```
#/app/config/config.yml or in parameters.yml
parameters:
    oxygem_oauth_client.login_success_route: custom_route_name_after_login_success
    oxygem_oauth_client.login_success_template: AcmeBundle:Security:custom_view_after_login_success.html.twig
    oxygem_oauth_client.login_route: custom_login_route_name_after_login_success # default fos_user_security_login
```

# Events

6 events have been created :
- Pre-authentication : oxygem.oauth2.pre_authentication
- Post-authentication-success : oxygem.oauth2.post_authentication.success
- Post-authentication-failure : oxygem.oauth2.post_authentication.failure
- Pre-refresh-token : oxygem.oauth2.pre_refresh
- Post-refresh-token-success : oxygem.oauth2.post_refresh.success
- Post-refresh-token-failure : oxygem.oauth2.post_refresh.failure

To use them :
```
namespace AcmeBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Oxygem\Bundle\OAuthClientBundle\Event\OAuth2AuthenticationEvent;
use Oxygem\Bundle\OAuthClientBundle\PostAuthenticationFailureEvents;
use Oxygem\Bundle\OAuthClientBundle\PostAuthenticationSuccessEvents;
use Oxygem\Bundle\OAuthClientBundle\PreAuthenticationEvents;
use Oxygem\Bundle\OAuthClientBundle\PreRefreshEvents;
use Oxygem\Bundle\OAuthClientBundle\PostRefreshFailureEvents;
use Oxygem\Bundle\OAuthClientBundle\PostRefreshSuccessEvents;

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
    oxygem_radins_user.oauth2_listener:
        class: AcmeBundle\EventListener\OAuth2Subscriber
        tags:
            - { name: kernel.event_subscriber }
```