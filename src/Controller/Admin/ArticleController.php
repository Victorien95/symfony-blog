<?php


namespace App\Controller\Admin;


use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ArticleController
 * @package App\Controller\Admin
 * @Route("/article")
 */
class ArticleController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function index(ArticleRepository $repository)
    {
        $articles = $repository->findBy([], ['publicationDate' => 'DESC']);
        return $this->render('admin/article/index.html.twig',
            [
                'articles' => $articles
            ]);
    }

    /**
     * @Route("/edition/{id}", defaults={"id":null}, requirements={"id" : "\d+"})
     */
    public function edit(Request $request, EntityManagerInterface $manager, $id)
    {
        /*
         * Intégrer le formulaire pour l'enregistrement d'un article Validation: tous les champs obligatoires
         * Avant l'enregistrmeent setter la date de publication à maintenant
         * et l'auteur avec l'utilisateur connecté ($this->>getUser() dans un contrôleur
         */

        if (is_null($id)){
            $article = new Article();
        }else{
            $article = $manager->find(Article::class, $id);
            if (is_null($article)){
                throw new NotFoundHttpException();
            }
        }
        $article->setAuthor($this->getUser());

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted()){
            if ($form->isValid()){

                /** @var UploadedFile|null $image */
                $image = $article->getImage();
                if(!is_null($image))
                {
                    // nom sous lequel on va enregistrer l'image
                    $filename = uniqid() . '.' . $image->guessExtension();

                    // déplace l'image uploadée
                    $image->move(
                        // dans quel répertoire
                        // cf config/services.yaml
                        $this->getParameter('upload_dir'),
                        // avec quel nom
                        $filename
                    );
                }

                $manager->persist($article);
                $manager->flush();

                $this->addFlash('success', 'L\'article "' . $article->getTitle() . '" est enregistrée');

                return $this->redirectToRoute('app_admin_article_index');

            }else{
                $this->addFlash('error', 'Le formulaire contient des erreurs');
            }
        }
        return $this->render('admin/article/edit.html.twig',
            [
                // passage du formulaire au template
                'form' => $form->createView()
            ]
        );
    }


    /**
     * Paramconverter : le paramètre typé Category contient la catégorie dont l'id est passé dans la aprtie varibale de l'Url
     * @Route("/supression/{id}", requirements={"id" : "\d+"})
     */
    public function delete(EntityManagerInterface $manager, Article $article)
    {
        // supression en bdd
        $manager->remove($article);
        $manager->flush();

        $this->addFlash('success', 'L\'article "' . $article->getTitle() . '" a bien ete supprimé');

        return $this->redirectToRoute('app_admin_article_index');
    }



}