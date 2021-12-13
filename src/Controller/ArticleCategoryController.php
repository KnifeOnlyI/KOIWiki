<?php

namespace App\Controller;

use App\Entity\ArticleCategory;
use App\Entity\User;
use App\Form\ArticleCategoryFormType;
use App\Repository\ArticleCategoryRepository;
use App\Service\UserService;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Represent the controller for ArticleCategory
 *
 * @author Dany Pignoux <dany.pignoux@outlook.fr>
 */
class ArticleCategoryController extends AbstractController
{
    /**
     * @var ObjectManager The entity manager
     */
    private ObjectManager $entityManager;

    /**
     * Create a new article article-category controller
     *
     * @param ArticleCategoryRepository $articleCategoryRepository The article article-category repository
     * @param ManagerRegistry $managerRegistry The manager registry
     * @param UserService $userService The user service
     */
    public function __construct(
        private ArticleCategoryRepository $articleCategoryRepository,
        private ManagerRegistry $managerRegistry,
        private UserService $userService
    ) {
        $this->entityManager = $this->managerRegistry->getManager();
    }

    /**
     * Get all category article
     *
     * @return Response The response
     */
    #[Route(path: '/article/category', name: 'get_all_category_articles', methods: ['GET'])]
    public function getAll(): Response
    {
        $categories = $this->articleCategoryRepository->findAll();

        return $this->render('article-category/list.html.twig', [
            'title' => 'Toutes les catégories',
            'categories' => $categories
        ]);
    }

    /**
     * Create a new article article-category
     *
     * @param Request $request The request
     *
     * @return Response The response
     */
    #[Route(path: '/article/category/new', name: 'new_article_category', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        if (!$this->userService->hasRole($this->getUser(), 'ARTICLE_CATEGORY', 'CREATE')) {
            throw $this->createNotFoundException();
        }

        $newArticleCategory = new ArticleCategory();

        $form = $this->createForm(ArticleCategoryFormType::class, $newArticleCategory);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newArticleCategory = $form->getData();

            $this->entityManager->persist($newArticleCategory);
            $this->entityManager->flush();

            return $this->redirectToRoute('get_all_category_articles');
        }

        return $this->render('article-category/new.html.twig', [
            'form' => $form->createView(),
            'title' => 'Création d\'une nouvelle catégorie'
        ]);
    }

    /**
     * Edit an article
     *
     * @param Request $request The request
     * @param int $id The ID of article to edit
     *
     * @return Response The response
     */
    #[Route(path: '/article/category/edit/{id}', name: 'edit_article_category', requirements: ['id' => '\d+'], methods: [
        'GET',
        'POST'
    ])]
    public function edit(Request $request, int $id): Response
    {
        $articleCategory = $this->articleCategoryRepository->find($id);

        if (!$this->getUser() || !$articleCategory || !$this->userCanEditArticleCategory($this->getUser())
        ) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(ArticleCategoryFormType::class, $articleCategory);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $articleCategory->setName($form['name']->getData());

            $this->entityManager->flush();

            return $this->redirectToRoute('get_all_category_articles');
        }

        return $this->render('article-category/edit.html.twig', [
            'form' => $form->createView(),
            'title' => 'Édition d\'une catégorie',
            'articleCategory' => $articleCategory
        ]);
    }

    /**
     * Delete one article category
     *
     * @param int $id The ID of article category to delete
     *
     * @return Response The response
     */
    #[Route(path: '/article/category/delete/{id}', name: 'delete_article_category', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function delete(int $id): Response
    {
        $articleCategory = $this->articleCategoryRepository->find($id);

        if (!$articleCategory || !$this->getUser()) {
            throw $this->createNotFoundException();
        }

        if (!$this->userCanDeleteArticle($this->getUser())) {
            throw $this->createAccessDeniedException();
        }

        $this->entityManager->remove($articleCategory);
        $this->entityManager->flush();

        $this->addFlash('success', 'La catégorie a été supprimé');

        return $this->redirectToRoute('get_all_category_articles');
    }

    /**
     * Check if the specified user can delete articles
     *
     * @param ?User $user The user
     *
     * @return bool TRUE if the user can delete articles, FALSE otherwise
     */
    private function userCanDeleteArticle(?UserInterface $user): bool
    {
        return $this->userService->hasRole($user, 'ARTICLE_CATEGORY', 'DELETE');
    }

    /**
     * Check if the specified user can edit categories
     *
     * @param ?User $user The user
     *
     * @return bool TRUE if the user can edit categories, FALSE otherwise
     */
    private function userCanEditArticleCategory(?UserInterface $user): bool
    {
        return $this->userService->hasRole($user, 'ARTICLE_CATEGORY', 'EDIT');
    }
}