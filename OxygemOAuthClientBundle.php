<?php

namespace Oxygem\Bundle\OAuthClientBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Oxygem\Bundle\OAuthClientBundle\DependencyInjection\Security\Factory\OAuthFactory;

class OxygemOAuthClientBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new OAuthFactory());
    }
}
