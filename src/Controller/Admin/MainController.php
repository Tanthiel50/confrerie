<?php

namespace App\Controller\Admin;

use App\Repository\ArticlesRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin', name: 'admin_')]
class MainController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Security $security, ArticlesRepository $articlesRepository): Response
    {
        if ($security->isGranted('ROLE_ADMIN')) {
            return $this->render('admin/index.html.twig');
        } elseif ($security->isGranted('ROLE_EDITEUR')) {
            return $this->render('admin/index.html.twig');
        }else{
            $articles = $articlesRepository->findAll();
            return $this->render('main/index.html.twig', [
                'articles' => $articles, 
            ]);
        }
    }
}