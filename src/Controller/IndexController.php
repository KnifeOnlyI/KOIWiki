<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
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
    /**
     * Index page
     *
     * @param ArticleRepository $articleRepository The article repository
     *
     * @return Response The response
     */
    #[Route(path: '/', name: 'index', methods: ['GET'])]
    public function get(ArticleRepository $articleRepository): Response
    {
        return $this->render('index/index.html.twig', [
            'title' => 'Derniers articles',
            'articles' => $articleRepository->getLastPublicArticles()
        ]);
    }
}