<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Form\ArticlesType;
use App\Entity\Categories;
use App\Entity\User;
use App\Form\CategoriesType;
use App\Repository\CategoriesRepository;
use App\Repository\ArticlesRepository;
use App\Service\PictureService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/articles')]
class ArticlesController extends AbstractController
{

    #[Route('/', name: 'app_articles_index', methods: ['GET'])]
    public function index(ArticlesRepository $articlesRepository): Response
    {
        return $this->render('admin/articles/index.html.twig', [
            'articles' => $articlesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_articles_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, PictureService $pictureService): Response
    {
        $article = new Articles();
        $form = $this->createForm(ArticlesType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer l'image
            $img = $form->get('img')->getData();
            // Enregistrer l'image
            $newImg = $pictureService->add($img, 'articles');
            // Mettre à jour l'entité
            $article->setImg($newImg);


            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('app_articles_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('articles/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_articles_show', methods: ['GET'])]
    public function show(int $id, ArticlesRepository $articlesRepository): Response
{
    $article = $articlesRepository->find($id);

    if (!$article) {
        throw $this->createNotFoundException('No article found for id ' . $id);
    }

    return $this->render('articles/show.html.twig', [
        'article' => $article,
    ]);
    }

    #[Route('/{id}/edit', name: 'app_articles_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id, ArticlesRepository $articlesRepository, EntityManagerInterface $entityManager, PictureService $pictureService): Response
{
    $article = $articlesRepository->find($id);

    if (!$article) {
        throw $this->createNotFoundException('No article found for id ' . $id);
    }

    $form = $this->createForm(ArticlesType::class, $article);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $img = $form->get('img')->getData();
        if ($img) {
            $newImg = $pictureService->add($img, 'articles');
            $article->setImg($newImg);
        }
        $entityManager->flush();

        return $this->redirectToRoute('app_articles_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->renderForm('articles/edit.html.twig', [
        'article' => $article,
        'form' => $form,
    ]);
    }

    #[Route('/{id}', name: 'app_articles_delete', methods: ['POST'])]
    public function delete(Request $request, int $id, ArticlesRepository $articlesRepository, EntityManagerInterface $entityManager): Response
    {
        $article = $articlesRepository->find($id);

        if (!$article) {
            throw $this->createNotFoundException('No article found for id ' . $id);
        }

        if ($this->isCsrfTokenValid('delete' . $id, $request->request->get('_token'))) {
            $entityManager->remove($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_articles_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/author', name: 'app_articles_author', methods: ['GET'])]
    public function showAuthor(int $id, ArticlesRepository $articlesRepository): Response
    {
        $article = $articlesRepository->find($id);

        if (!$article) {
            throw $this->createNotFoundException('No article found for id ' . $id);
        }

        // Your existing logic to get the author and author's articles
        $author = $article->getUser();
        $authorArticles = $author->getArticles(); // Récupérer les articles de l'auteur

        return $this->render('articles/author.html.twig', [
            'article' => $article,
            'author' => $author,
            'authorArticles' => $authorArticles,
        ]);
    }

    #[Route('/{slug}/read', name: 'app_articles_reading', methods: ['GET'])]
    public function showArticlestring($slug, ArticlesRepository $articlesRepository): Response
    {
        $article = $articlesRepository->findOneBySlug($slug);

        if (!$article) {
            throw $this->createNotFoundException('No article found for slug ' . $slug);
        }
        $author = $article->getUser();
        $articleCategory = $article->getCategory();

        return $this->render('articles/read.html.twig', [
            'article' => $article,
            'author' => $author,
            'articleCategory' => $articleCategory
        ]);
    }
}
