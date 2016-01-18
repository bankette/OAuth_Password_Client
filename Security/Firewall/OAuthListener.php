<?php
/**
 * Created by PhpStorm.
 * User: julienmaquet
 * Date: 14/08/15
 * Time: 10:32
 */

namespace Oxygem\Bundle\OAuthClientBundle\Security\Firewall;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Oxygem\Bundle\OAuthClientBundle\Security\Authentication\OAuthUserToken;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;

use Oxygem\Bundle\OAuthClientBundle\Event\OAuth2AuthenticationEvent;
use Oxygem\Bundle\OAuthClientBundle\PostAuthenticationFailureEvents;
use Oxygem\Bundle\OAuthClientBundle\PostAuthenticationSuccessEvents;
use Oxygem\Bundle\OAuthClientBundle\PreAuthenticationEvents;
use Oxygem\Bundle\OAuthClientBundle\PreRefreshEvents;
use Oxygem\Bundle\OAuthClientBundle\PostRefreshFailureEvents;
use Oxygem\Bundle\OAuthClientBundle\PostRefreshSuccessEvents;

class OAuthListener implements ListenerInterface
{

    private $securityContext;
    public function __construct(
        SecurityContextInterface $securityContext,
        AuthenticationManagerInterface $authenticationManager,
        EventDispatcher $eventDispatcher,
        $session
    )
    {
        $this->securityContext = $securityContext;
        $this->authenticationManager = $authenticationManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->session = $session;
    }

    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $oauthEvent = new OAuth2AuthenticationEvent($this->securityContext);
        if(
            $request->request->get("_username") !== null
            && $request->request->get("_password") !== null
        ){
            $token = new OAuthUserToken();
            $token->setUser($request->request->get("_username"));
            $token->setPassword($request->request->get("_password"));

            try {
                $this->eventDispatcher->dispatch(PreAuthenticationEvents::OAUTH2_PRE_AUTHENTICATION, $oauthEvent);
                $authToken = $this->authenticationManager->authenticate($token);
                $authToken->setAuthenticated(true);
                $this->securityContext->setToken($authToken);
                $this->eventDispatcher->dispatch(PostAuthenticationSuccessEvents::OAUTH2_POST_AUTHENTICATION_SUCCESS, $oauthEvent);
            } catch (AuthenticationException $failed) {
                // To deny the authentication clear the token.
                // Make sure to only clear your token, not those of other authentication listeners.
                $token = $this->securityContext->getToken();
                if ($token instanceof OAuthUserToken) {
                    $this->securityContext->setToken(null);
                }
                $this->eventDispatcher->dispatch(PostAuthenticationFailureEvents::OAUTH2_POST_AUTHENTICATION_FAILURE, $oauthEvent);
            }
        }else{
            $token = $this->securityContext->getToken();
            if($token instanceof OAuthUserToken){
                if( time() > $token->getExpireTime()){
                    try {
                        $this->eventDispatcher->dispatch(PreRefreshEvents::OAUTH2_PRE_REFRESH, $oauthEvent);
                        $newToken = $this->authenticationManager->refresh($token);
                        $this->securityContext->setToken($newToken);
                        $this->eventDispatcher->dispatch(PostRefreshSuccessEvents::OAUTH2_POST_REFRESH_SUCCESS, $oauthEvent);
                    } catch (AuthenticationException $failed) {
                        // To deny the authentication clear the token.
                        // Make sure to only clear your token, not those of other authentication listeners.
                        $token = $this->securityContext->getToken();
                        if ($token instanceof OAuthUserToken) {
                            $this->securityContext->setToken(null);
                        }
                        $this->eventDispatcher->dispatch(PostRefreshFailureEvents::OAUTH2_POST_REFRESH_FAILURE, $oauthEvent);
                    }
                }
            }
        }
        // elsewhere we do nothing
        return;
    }
}