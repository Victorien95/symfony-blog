<?php


namespace App\Controller\Admin;


use App\Entity\Article;
use App\Entity\Comment;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CommentsController
 * @package App\Controller\Admin
 * @Route("/comments")
 */
class CommentsController extends AbstractController
{
    /**
     * @Route("/{id}")
     */
    public function index(CommentRepository $commentRepository, Article $article)
    {
        $commentaires = $commentRepository->findBy(['article' => $article], ['publicationDate' => 'DESC']);
        return $this->render('admin/comments/index.html.twig',
            [
                'commentaires' => $commentaires,
                'article' => $article
            ]);
    }

    /**
     * @param EntityManagerInterface $manager
     * @param Comment $comment
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/delete/{id}")
     */
    public function delete(EntityManagerInterface $manager, Comment $comment)
    {

        $manager->remove($comment);
        $manager->flush();

        $this->addFlash('success', 'Le commentaire "' . $comment->getArticle()->getTitle() . '" a bien ete supprimé');

        return $this->redirectToRoute('app_admin_article_index');
    }

}