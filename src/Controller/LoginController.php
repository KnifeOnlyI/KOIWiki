<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Controller to manage login
 *
 * @author Dany Pignoux <dany.pignoux@outlook.fr>
 */
class LoginController extends AbstractController
{
    /**
     * Page to manage login page
     *
     * @param AuthenticationUtils $authenticationUtils The authentication utils
     *
     * @return Response The response
     */
    #[Route('/login', name: 'login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser() !== null) {
            return $this->redirectToRoute('index');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        if ($error !== null) {
            $this->addFlash('error', 'Identifiants de connexion invalides');
        }

        return $this->render('login/index.html.twig', [
            'title' => 'Login',
            'last_username' => $lastUsername,
        ]);
    }
}
