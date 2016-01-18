<?php
/**
 * Created by PhpStorm.
 * User: renaud
 * Date: 24/06/15
 * Time: 11:43
 */

namespace Oxygem\Bundle\OAuthClientBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Oxygem\Bundle\OAuthClientBundle\Security\Authentication\OAuthUserToken;

class SecurityController extends Controller
{
    function loginCheckAction(){
        if(
            $this->get("security.context")->getToken() instanceof OAuthUserToken
            && $this->get("security.context")->getToken()->isAuthenticated()
        ){
            return $this->redirect($this->generateUrl($this->container->getParameter('oxygem_oauth_client.login_success_route')));
        }else{
            $this->get('session')->getFlashBag()->add(
                'error',
                $this->get('translator')->trans('oauth.login.error')
            );
            return $this->redirect($this->generateUrl($this->container->getParameter('oxygem_oauth_client.login_route')));
        }
    }

    function loginSuccessAction(){
        return $this->render($this->container->getParameter('oxygem_oauth_client.login_success_template'));
    }

}
