<?php
namespace Oxygem\Bundle\OAuthClientBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Security\Core\SecurityContext;

class OAuth2AuthenticationEvent extends Event
{
    protected $securityContext;

    public function __construct(SecurityContext $securityContext){
        $this->securityContext = $securityContext;
    }

    public function getSecurityContext(){
        return $this->securityContext;
    }

}