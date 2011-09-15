<?php
/**
 * Epixa - SimpleUser
 */

namespace Epixa\SimpleUserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\Security\Core\SecurityContext,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template,
    Symfony\Component\HttpFoundation\Request,
    Epixa\TalkfestBundle\Entity\User,
    Epixa\SimpleUserBundle\Form\Type\UserType;

/**
 * Controller managing the authentication of users
 *
 * @category   SimpleUser
 * @package    Controller
 * @copyright  2011 epixa.com - Court Ewing
 * @license    Simplified BSD
 * @author     Court Ewing (court@epixa.com)
 */
class RegistrationController extends Controller
{
    /**
     * @Route("/signup", name="signup")
     * @Template()
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return array
     */
    public function signupAction(Request $request)
    {
        $user = new User();

        $form = $this->createForm(new UserType(), $user);

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($user);
                $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
                $user->setPassword($password);
                
                $this->getUserService()->add($user);

                $this->get('session')->setFlash('notice', 'Your account has been created!');
                return $this->redirect("/");
            }
        }

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * Gets the user service
     *
     * @return \Epixa\SimpleUserBundle\Service\UserService
     */
    public function getUserService()
    {
        return $this->get('epixa_simple_user.service.user');
    }
}