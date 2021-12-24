<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Form\ArticleFormType;
use App\Repository\ArticleCategoryRepository;
use App\Repository\ArticleRepository;
use App\Service\UserService;
use DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Represent the controller for Articles
 *
 * @author Dany Pignoux <dany.pignoux@outlook.fr>
 */
class ArticleController extends AbstractController
{
    /**
     * @var ObjectManager The entity manager
     */
    private ObjectManager $entityManager;

    /**
     * Create a new article controller
     *
     * @param ArticleRepository $articleRepository The article repository
     * @param ArticleCategoryRepository $articleCategoryRepository The article category repository
     * @param ManagerRegistry $managerRegistry The manager registry
     * @param UserService $userService The user service
     */
    public function __construct(
        private ArticleRepository $articleRepository,
        private ArticleCategoryRepository $articleCategoryRepository,
        private ManagerRegistry $managerRegistry,
        private UserService $userService
    ) {
        $this->entityManager = $this->managerRegistry->getManager();
    }

    /**
     * Get all articles
     *
     * @return Response The response
     */
    #[Route(path: '/article', name: 'get_all_articles', methods: ['GET'])]
    public function getAll(): Response
    {
        return $this->render('article/list.html.twig', [
            'title' => 'Tous les articles',
            'articles' => $this->getAllArticlesForConnectedUser()
        ]);
    }

    /**
     * Get all article of the specified category
     *
     * @return Response The response
     */
    #[Route(path: '/article/category/{id}', name: 'get_all_articles_by_category_articles', methods: ['GET'])]
    public function getAllByCategory(int $id): Response
    {
        $category = $this->articleCategoryRepository->find($id);

        if (!$category) {
            throw $this->createNotFoundException();
        }

        $articlesTMP = $this->getAllArticlesForConnectedUser();

        $articles = [];

        foreach ($articlesTMP as $article) {
            if ($article->getCategory()->getId() == $id) {
                $articles[] = $article;
            }
        }

        return $this->render('article/list.html.twig', [
            'title' => 'Articles dans la catégorie : ' . $category->getName(),
            'articles' => $articles
        ]);
    }

    /**
     * Get one article
     *
     * @param int $id The ID of article to get
     *
     * @return Response The response
     */
    #[Route(path: '/article/{id}', name: 'get_article', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function get(int $id): Response
    {
        $article = $this->articleRepository->find($id);

        if (!$article ||
            (!$this->getUser() && !$article->getIsPublic()) ||
            (!$this->userService->hasRole($this->getUser(), 'ARTICLE', 'VIEW_PRIVATE')) &&
            $this->userService->equals($article->getAuthor(), $this->getUser()) &&
            !$article->getIsPublic()
        ) {
            throw $this->createNotFoundException();
        }

        return $this->render('article/view.html.twig', [
            'title' => $article->getTitle(),
            'canDelete' => $this->userCanDeleteArticle($this->getUser(), $article),
            'canEdit' => $this->userCanEditArticle($this->getUser(), $article),
            'article' => $article
        ]);
    }

    /**
     * Create a new article
     *
     * @param Request $request The request
     *
     * @return Response The response
     */
    #[Route(path: '/article/new', name: 'new_article', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        if (!$this->userService->hasRole($this->getUser(), 'ARTICLE', 'CREATE')) {
            throw $this->createNotFoundException();
        }

        $newArticle = new Article();
        $form = $this->createForm(ArticleFormType::class, $newArticle);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Article $newArticle */
            $newArticle = $form->getData();

            $newArticle->setCreatedAt(new DateTimeImmutable());
            $newArticle->setAuthor($this->getUser());

            $this->entityManager->persist($newArticle);
            $this->entityManager->flush();

            return $this->redirectToRoute('get_article', ['id' => $newArticle->getId()]);
        }

        return $this->render('article/new.html.twig', [
            'form' => $form->createView(),
            'title' => 'Création d\'un nouvel article'
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
    #[Route(path: '/article/edit/{id}', name: 'edit_article', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        $article = $this->articleRepository->find($id);

        if (!$this->getUser() || !$article ||
            !$this->userCanEditArticle($this->getUser(), $article)
        ) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(ArticleFormType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setTitle($form['title']->getData());
            $article->setCategory($form['category']->getData());
            $article->setContent($form['content']->getData());
            $article->setIsPublic($form['isPublic']->getData());
            $article->setLastUpdatedAt(new DateTimeImmutable());

            $this->entityManager->flush();

            return $this->redirectToRoute('get_article', ['id' => $article->getId()]);
        }

        return $this->render('article/edit.html.twig', [
            'form' => $form->createView(),
            'title' => 'Édition d\'un article',
            'article' => $article
        ]);
    }

    /**
     * Search articles
     *
     * @param Request $request The request
     *
     * @return Response The response
     */
    #[Route(path: '/article/search', name: 'search_article', methods: ['GET'])]
    public function search(Request $request): Response
    {
        $query = $request->query->get('q');

        if ($this->getUser()) {
            if ($this->userService->hasRole($this->getUser(), 'ARTICLE', 'VIEW_PRIVATE')) {
                $articles = $this->articleRepository->searchInPublicOrPrivate($query);
            } else {
                $articles = $this->articleRepository->searchInPublicAndPrivateUserAuthored($this->getUser(), $query);
            }
        } else {
            $articles = $this->articleRepository->searchInPublic($query);
        }

        return $this->render('article/list.html.twig', [
            'title' => 'Recherche d\'articles',
            'articles' => $articles,
            'q' => $query
        ]);
    }

    /**
     * Delete one article
     *
     * @param int $id The ID of article to delete
     *
     * @return Response The response
     */
    #[Route(path: '/article/delete/{id}', name: 'delete_article', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function delete(int $id): Response
    {
        $article = $this->articleRepository->find($id);

        if (!$article || !$this->getUser()) {
            throw $this->createNotFoundException();
        }

        if (!$this->userCanDeleteArticle($this->getUser(), $article)) {
            throw $this->createAccessDeniedException();
        }

        $this->entityManager->remove($article);
        $this->entityManager->flush();

        $this->addFlash('success', 'L\'article a été supprimé');

        return $this->redirectToRoute('get_all_articles');
    }

    /**
     * Get all articles for the connected user
     *
     * @return Article[] The founded articles
     */
    private function getAllArticlesForConnectedUser(): array
    {
        if ($this->getUser()) {
            if ($this->userService->hasRole($this->getUser(), 'ARTICLE', 'VIEW_PRIVATE')) {
                $articles = $this->articleRepository->findAll();
            } else {
                $articles = $this->articleRepository->getAllForUser($this->getUser());
            }
        } else {
            $articles = $this->articleRepository->getAllForAnonymousUser();
        }

        return $articles;
    }

    /**
     * Check if the specified user can delete the specified article
     *
     * @param ?User $user The user
     * @param Article $article The article to delete
     *
     * @return bool TRUE if the user can delete the article, FALSE otherwise
     */
    private function userCanDeleteArticle(?UserInterface $user, Article $article): bool
    {
        return
            $this->userService->hasRole($user, 'ARTICLE', 'DELETE_PRIVATE') ||
            $this->userService->equals($user, $article->getAuthor());
    }

    /**
     * Check if the specified user can edit the specified article
     *
     * @param ?User $user The user
     * @param Article $article The article to delete
     *
     * @return bool TRUE if the user can edit the article, FALSE otherwise
     */
    private function userCanEditArticle(?UserInterface $user, Article $article): bool
    {
        return $this->userService->equals($user, $article->getAuthor());
    }
}