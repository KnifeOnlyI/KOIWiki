<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Represent the controller for index
 *
 * @author Dany Pignoux <dany.pignoux@outlook.fr>
 */
class IndexController extends AbstractController
{
    #[Route(path: '/', name: 'index')]
    public function get(): Response
    {
        return $this->render('index/index.html.twig', [
            'title' => 'Accueil'
        ]);
    }
}