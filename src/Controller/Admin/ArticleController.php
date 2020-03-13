<?php


namespace App\Controller\Admin;


use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

        //cf originalImage on set a null pour eviter erreur
        $originalImage = null;

        if (is_null($id)){
            $article = new Article();
        }else{
            $article = $manager->find(Article::class, $id);
            if (is_null($article)){
                throw new NotFoundHttpException();
            }
            if (!is_null($article->getImage())){

                // nom du fichier venant de la bdd / On stock dans une varibale pour l'utiliser si on upload pas de nouvelle image
                $originalImage = $article->getImage();
                // pour le champ de formulaire qui attend un objet File
                $article->setImage(new File($this->getParameter('upload_dir') . $article->getImage())
                );
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
                    $article->setImage($filename);

                    // supression de l'ancienne
                    // s'il y en a
                    if (!is_null($originalImage)){
                        unlink($this->getParameter('upload_dir') . $originalImage);
                    }
                }else{
                    // pour la modification; sans upload on remet le nom de l'image venant de la bdd
                    $article->setImage($originalImage);
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
                'form' => $form->createView(),
                'original_image' => $originalImage
            ]
        );
    }


    /**
     * Paramconverter : le paramètre typé Category contient la catégorie dont l'id est passé dans la aprtie varibale de l'Url
     * @Route("/supression/{id}", requirements={"id" : "\d+"})
     */
    public function delete(EntityManagerInterface $manager, Article $article)
    {
        if (!is_null($article->getImage())){
            $image = $this->getParameter('upload_dir') . $article->getImage();
            if(file_exists($image))
            {
                unlink($image);
            }
        }
//        $image = $article->getImage();
//        $this->delete('upload_dir', $image);
        // supression en bdd
        $manager->remove($article);
        $manager->flush();

        $this->addFlash('success', 'L\'article "' . $article->getTitle() . '" a bien ete supprimé');

        return $this->redirectToRoute('app_admin_article_index');
    }

    /**
     * @Route("/ajax/{id}")
     */
    public function ajax(Article $article)
    {
        // en text brute
       //$reponse = new Response(nl2br($article->getContent()));

       //return $reponse;

        ////        ou en twig

        return $this->render('admin/article/ajax.html.twig', ['article' => $article]);

        ////        ou en json

//        $reponse = [
//            'titre' => $article->getTitle(),
//            'contenu' => $article->getContent()
//        ];
//
//        return new JsonResponse($reponse);
    }

}