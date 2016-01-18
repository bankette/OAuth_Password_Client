<?php
namespace Oxygem\Bundle\OAuthClientBundle\DependencyInjection\Security\Factory;

/**
 * Created by PhpStorm.
 * User: julienmaquet
 * Date: 14/08/15
 * Time: 10:50
 */

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AbstractFactory;


class OAuthFactory extends AbstractFactory {
    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {
        $providerId = 'oxygem.oauth.security.authentication.provider.'.$id;
        $container
            ->setDefinition($providerId, new DefinitionDecorator('oxygem.oauth.security.authentication.provider'))
            ->replaceArgument(0, new Reference($userProvider))
        ;

        $listenerId = 'oxygem.oauth.security.authentication.listener.'.$id;
        $container
            ->setDefinition($listenerId, new DefinitionDecorator('oxygem.oauth.security.authentication.listener'))
            ->replaceArgument(1, new Reference($providerId))
        ;

        return array($providerId, $listenerId, $defaultEntryPoint);
    }

    public function getPosition()
    {
        return 'pre_auth';
    }

    public function getKey()
    {
        return 'dealfeedapi_oauth';
    }

    protected function createAuthProvider(ContainerBuilder $container, $id, $config, $userProviderId){
        return "oxygem.oauth.security.authentication.provider";
    }

    protected function getListenerId(){
        return "oxygem.oauth.security.authentication.listener";
    }
}