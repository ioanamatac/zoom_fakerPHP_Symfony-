<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Form\ArticleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\ArticleRepository;
use App\Entity\Article;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index (ArticleRepository $repo, Request $request, PaginatorInterface $paginator): Response
    {
       $donnees = $articles = $repo->findAll();
       $articles = $paginator->paginate(
           $donnees,
           $request->query->getInt('page', 1),   //Numero de la page en cours, 1 par default
          4
       );
        return $this->render('blog/index.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/", name="home")
     */
    public function home(): Response
    {
        return $this->render('blog/home.html.twig');
    }
    
    /**
     * @Route("/blog/new", name="blog_create")
     * @Route("/blog/{id}/edit", name="blog_edit")
     */
    public function form(Article $article = null, Request $request, EntityManagerInterface $entityManager): Response {
        
        if(!$article){
            $article = new Article();
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                if(!$article->getId()){
                    $article->setCreatedAt(new \DateTime());
                }
                $article = $form->getData();               
                $entityManager->persist($article);
                $entityManager->flush(); 

                return $this->redirectToRoute('blog_show', [ 
                    'id'=>$article->getId()]);
                }        
        return $this->render('blog/create.html.twig', [
            'formArticle' => $form->createView(),
            'editMode'=>$article->getId() !== null
        ]);
    }
    
    /**
     * @Route("/blog/{id}", name="blog_show")
     */
    public function show(Article $article): Response {

        return $this->render('blog/show.html.twig',[
            'article' => $article
        ]);
    }   
}
