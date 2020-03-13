<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ArticleController
 * @package App\Controller
 * @Route("/articles")
 */
class ArticleController extends AbstractController
{

//    /**
//     * @Route("/acceuil", name="article")
//     */
//    public function index(ArticleRepository $repository)
//    {
//        $all_articles = $repository->findBy([], ['publicationDate' => 'DESC']);
//
//        return $this->render('article/acceuil.html.twig',
//            [
//                'all_articles' => $all_articles
//            ]);
//    }
//
//    /**
//     * @param ArticleRepository $repository
//     * @param $id
//     * @return \Symfony\Component\HttpFoundation\Response
//     * @Route("/article/{id}")
//     */
//    public function articlePresentation($id, Article $article)
//    {
//
//        return $this->render('article/presentation.html.twig',
//            [
//                'article' => $article
//            ]
//            );
//    }

    /**
     * @Route("/{id}")
     */
    public function index(Article $article, CommentRepository $comments, Request $request, EntityManagerInterface $manager)
   {
       $myComment = $comments->findBy(['article' => $article], ['publicationDate' => 'DESC']);
       $commentaire = new Comment();
       $commentaire->setUser($this->getUser());
       $commentaire->setArticle($article);
       $form = $this->createForm(CommentType::class, $commentaire);
       $form->handleRequest($request);
       if ($form->isSubmitted())
       {
           if ($form->isValid()){
               $manager->persist($commentaire);
               $manager->flush();
               $this->addFlash('success', 'Votre commentaire a été ajouté');
               // permet d'empecher le renvoi du post avec un F5(actualisation). On redirige sur la meme page pour etre en GET et non plus en POST
               return $this->redirectToRoute('app_article_index', ['id' => $article->getId()]);
               //variante avec route adaptative à la méthode en cours -> $request->get('_route') => renverra l'url de index ici
               // return $this->redirectToRoute($request->get('_route'), ['id' => $article->getId()]);

           }

       }
        return $this->render('article/index.html.twig',
            [
                'article' => $article,
                'comments' => $myComment,
                'user' => $this->getUser(),
                'form' => $form->createView()
            ]
        );
   }



}
