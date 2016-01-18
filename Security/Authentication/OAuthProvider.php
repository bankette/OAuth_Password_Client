<?php
/**
 * Created by PhpStorm.
 * User: julienmaquet
 * Date: 14/08/15
 * Time: 10:48
 */

namespace Oxygem\Bundle\OAuthClientBundle\Security\Authentication;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class OAuthProvider implements AuthenticationProviderInterface {
    protected $dealfeedApiUrl;
    protected $dealfeedApiId;
    protected $dealfeedApiSecret;
    protected $userManager;

    public function __construct(
        $userManager,
        $dealfeedApiUrl,
        $dealfeedApiId,
        $dealfeedApiSecret
    )
    {
        $this->userManager = $userManager;
        $this->dealfeedApiUrl = $dealfeedApiUrl;
        $this->dealfeedApiId = $dealfeedApiId;
        $this->dealfeedApiSecret = $dealfeedApiSecret;
    }

    public function authenticate(TokenInterface $token)
    {
        if(strlen($token->getOAuthToken())===0){
            $url = $this->dealfeedApiUrl."/oauth/v2/token?"
                ."client_id=".$this->dealfeedApiId
                ."&client_secret=".$this->dealfeedApiSecret
                ."&grant_type=password"
                ."&username=".$token->getUser()
                ."&password=".$token->getPassword();

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $apiResponse = json_decode(curl_exec($ch));
            curl_close($ch);

            if (isset($apiResponse->access_token)) {
                $user = $this->userManager->createUser();;
                $user->setUsername($token->getUsername());
                $authenticatedToken = new OAuthUserToken($user->getRoles());
                $authenticatedToken->setUser($user);
                $authenticatedToken->setOAuthToken($apiResponse->access_token);
                $authenticatedToken->setRefreshToken($apiResponse->refresh_token);
                $authenticatedToken->setTokenType($apiResponse->token_type);
                // We take 3 minutes less (180 seconds) just to be sure.
                $authenticatedToken->setExpireTime(time() + $apiResponse->expires_in - 180);
                return $authenticatedToken;
            }elseif(isset($apiResponse->error_description)) {
                throw new AuthenticationException($apiResponse->error_description);
            }else{
                throw new AuthenticationException('The OAuth authentication failed.');
            }
        }else{
            return $token;
        }


    }

    public function refresh(OAuthUserToken $token)
    {
        // BE CAREFUL !!! argument order is very important !
        // if you change the order of parameters, refresh will not work and the API will give a client credential error.
        $url = $this->dealfeedApiUrl."/oauth/v2/token?"
            ."client_secret=".$this->dealfeedApiSecret
            ."&client_id=".$this->dealfeedApiId
            ."&refresh_token=".$token->getRefreshToken()
            ."&grant_type=refresh_token";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $apiResponse = json_decode(curl_exec($ch));
        curl_close($ch);

        if (isset($apiResponse->access_token)) {
            $token->setOAuthToken($apiResponse->access_token);
            $token->setRefreshToken($apiResponse->refresh_token);
            $token->setTokenType($apiResponse->token_type);
            // We take 3 minutes less (180 seconds) just to be sure.
            $token->setExpireTime(time() + $apiResponse->expires_in - 180);
        }elseif(isset($apiResponse->error_description)) {
            throw new AuthenticationException($apiResponse->error_description);
        }else{
            throw new AuthenticationException('The OAuth refresh failed.');
        }
    return $token;
    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof OauthUserToken;
    }
}