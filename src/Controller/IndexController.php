<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function index(ArticleRepository $repository)
    {
        // Les 3 derniers articles du blog:
        $articles = $repository->findBy([], ['publicationDate' => 'DESC'], 3);
        return $this->render('index/index.html.twig',
            [
                'articles' => $articles
            ]);
    }
}
