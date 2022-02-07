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
                    //$now = new \DateTime();
                   
                    $days = (new \DateTime())->diff($article->getCreatedAt())->days;
                    //$days = $interval->days;
                    // $minimum = '-' . $days . 'days';
                    $comment->setCreatedAt($faker->dateTimeBetween('-' . $days . 'days'))
                    ->setArticle($article);
                    $manager->persist($comment);
                }
            }        

        }
        $manager->flush();
       
    }
    
}
