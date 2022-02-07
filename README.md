
# ZOOM SUR LES FIXTURES(BLOG DEMO PHP SYMFONY - MySQL) 
# FakerPHP


## Table of Contents
1. [General Info](#general-info)
2. [Technologies](#technologies)
3. [Installation](#installation)
4. [Project Directory](#project-directory)
5. [DB Configuration](#db-configuration)
6. [Controller](#controller)
7. [Entity](#entity)
8. [Template](#template)
9. [DoctrineFixturesBundle](#doctrine-fixtures-bundle)
10. [Writing Fixtures](#writing-fixtures)
11. [Loading Fixtures](#loading-fixtures)
12. [Form](#form)
13. [Bootstrap](#bootstrap)
13. [Add FormArticle](#add-article)

# General Info
Exemples de fixtures / formulaire dans la creation d'un blog demo basé sur le tutoriel de [Lior Chamla](https://www.youtube.com/LiorChamla).

# Technologies
* O.S. Windows 10
* PHP 7.2.5
* Symfony 5.4
* MySQL 5.7

# Installation
* git clone https://github.com/ioanamatac/zoom_fakerPHP_Symfony-.git
* cd project
* php bin/console server:run

# Project create
>Creation projet : 
* composer create-project symfony/website-skeleton demoFixturesBlog
* cd demoFixturesBlog

# DB Configuration
> Dans le fichier .env :
```DATABASE_URL="mysql://root@127.0.0.1:3306/bfixtures?serverVersion=5.7"```
# Controller 
>Creation d'un controleur : 
* composer require doctrine maker –dev
* php bin/console make:controller 
# Entity
>Dans cet exemple j'ai utilisé juste 3 classes: Article, Category et Comment.
>Ex :creation d'une classe :
* php bin/console make:entity

```php
 Class name of the entity to create or update (e.g. GrumpyPuppy):
 > Category

 created: src/Entity/Category.php
 created: src/Repository/CategoryRepository.php
 
 Entity generated! Now let's add some fields!
 You can always add more fields later manually or by re-running this command.

 New property name (press <return> to stop adding fields):
 > title

 Field type (enter ? to see all types) [string]:
 >

 Field length [255]:
 >    

 Can this field be null in the database (nullable) (yes/no) [no]:
 >

 updated: src/Entity/Category.php

 Add another property? Enter the property name (or press <return> to stop adding fields):
 > description

 Field type (enter ? to see all types) [string]:
 > text

 Can this field be null in the database (nullable) (yes/no) [no]:
 > yes

 updated: src/Entity/Category.php
```
# Template
>Exemple: dans le Controleur
```php 
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
```
>Exemple de vue: index.html.twig
```php
{% extends 'base.html.twig' %}

{% block title %}BlogController!{% endblock %}

{% block body %}        
        <div class="container">
             <section class="articles">
                {% for article in articles %}
                    <article>
                    <!-- Card Regular -->
                        <div class="card card-cascade mt-3">
                             <!-- Card content -->
                            <div class="card-body card-body-cascade">
                                <!-- Title -->
                                <h4 class="card-title"><strong>{{ article.title }}</strong></h4>
                                <h4 class="card-image"><img src=" {{ article.image }}" alt=""></h4>                                
                                <!-- Text -->
                                <p class="card-text">Article écrit le {{ article.createdAt| date('d/m/Y') }} à {{ article.createdAt | date('H:i') }} dans la <i>Categorie </i>{{ article.category }}: </p> 
                                    {% for category in article.category %}
                                        {{ category.title }}
                                    {% endfor %}                                    
                                <p class="card-content"><i class="fas fa-quote-left pe-2"></i>{{ article.content }} </p><br />
                                    {% for comment in article.comments %}
                                        <p class="card-text"><i>Commentaire</i> écrit le {{ comment.createdAt| date('d/m/y') }} à {{comment.createdAt| date('H:i')}} par {{ comment.author }} <i class="fas fa-user-ninja vanished"></i>:</p>                                    
                                        <p class="card-content"> {{ comment.content }}
                                    {% endfor %}
                                <div><a href="{{ path('blog_show', {'id': article.id }) }}" class="btn btn-dark">Lire la suite</a></div>                                                               
                            </div>                         
                        </div>
                    </article>            
                {% endfor %}
                {{ knp_pagination_render(articles) }}           

             </section> 
        </div>       
{% endblock %}
``` 
# DoctrineFixturesBundle
>Les "fixtures" sont utilisés pour charger un « fake » ensemble de données dans une base de données qui peut ensuite être utilisée pour tester ou pour vous aider à obtenir des données intéressantes pendant que vous développez votre application.

>Installation:
* composer require --dev orm-fixtures 
* composer require fakerphp/faker 

# Writing Fixtures
>Exemple ArticleFixtures
```php
<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Article;
use App\Entity\Categorie;
use App\Entity\Comment;
use DateTime;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');
        //Creer des categories
        for ($i=1; $i <=10 ; $i++) { 
           $categorie =  new Category();
           $categorie->setTitle($faker->sentence());
           $categorie->setDescription($faker->paragraph());
           $manager ->persist($categorie);
             
                //Creer entre 4 et 6 articles
            for ($j=0; $j < mt_rand(4, 10) ; $j++) { 
                $article = new Article(); 
                $article->setTitle($faker->sentence());            
                $article->setContent($faker->text(200));
                $article->setImage($faker->imageUrl());
                $article->setCreatedAt($faker->dateTimeBetween('-6 month'));
                $article->setCategory($categorie);         
                $manager->persist($article);          
          
                //Creer des commentaires
                for ($k=0; $k < mt_rand(4, 10) ; $k++) { 
                    $comment  = new Comment();
                    $comment->setAuthor($faker->name());
                    $comment->setContent($faker->text(200));       
                   
                    $days = (new \DateTime())->diff($article->getCreatedAt())->days;                    
                    $comment->setCreatedAt($faker->dateTimeBetween('-' . $days . 'days'))
                    ->setArticle($article);
                    $manager->persist($comment);
                }
            }        

        }
        $manager->flush();
       
    }
    
}
```
# Loading Fixtures
* php bin/console doctrine:fixtures:load
# Form
>Creation d'un formulaire
* php bin/console make:form ArticleType
# Bootstrap
>Comment ajouter boostrap dans le formulaire: dans config->packages->twig.yaml:
```php
form_themes: ['bootstrap_5_layout.html.twig']
```
>Dans la vue:
```php
{% form_theme formArticle 'bootstrap_5_layout.html.twig' %}
```
# Create/Edit FormArticle
>Dans le Controleur:
```php
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
```
>ArticleType
```php
<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('content')
            ->add('image')
            ->add('createdAt')
            ->add('category')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
```
Et la vue que moi j'ai nommée create.html.twig :
```php
{% extends 'base.html.twig' %}

{% form_theme formArticle 'bootstrap_5_layout.html.twig' %}

{% block title %} Creation d'un article {% endblock %}

{% block body %} 
    <div class="container mt-3 ">
        <h3>Creation d'un article</h3>
        <div class="form-row ">
            <div class="form-group col-md-4 ">
                {{ form_start(formArticle) }}
                {{ form_widget(formArticle) }}
                    <button class="btn btn-success">
                        {% if editMode %}
                            Enregistrer les modifications
                        {% else %}
                            Ajouter article
                        {% endif %}
                    </button>
                {{ form_end(formArticle) }}
            
            </div>
        </div>
    </div>        

{% endblock %}
```
# Enjoy !
Ioana