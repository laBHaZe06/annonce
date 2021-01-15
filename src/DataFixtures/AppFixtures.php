<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Image;
use Cocur\Slugify\Slugify;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class AppFixtures extends Fixture
{   
     private $passwordEncoder; //on le met en private 

     public function __construct(UserPasswordEncoderInterface $passwordEncoder)
     {
         $this->passwordEncoder = $passwordEncoder;   // on encode le password entrer par le "user"
     }
            public function load(ObjectManager $manager)


            {
                $adminRole = new Role();
                $adminRole->setTitle('ROLE_ADMIN');   // creation d'un role utilisateur

                $manager->persist($adminRole);

                $adminUser = new User();
                $adminUser->setFirstName("Yoan")
                    ->setLastName("Borne")
                    ->setEmail("yop06@outlook.fr")
                    ->setPicture("https://via.placeholder.com/64")
                    ->setIntroduction("Je suis l'admin")
                    ->setDescription("administrateur du site")
                    ->setSlug("Yoan-Borne")
                    ->setHash($this->passwordEncoder->encodePassword(
                       $adminUser,
                        'password'
                     ))
                     ->addUserRole($adminRole);
                     $manager->persist($adminUser);


                for($k=1;$k<=5;$k++) {   // pour l'auteur 
                    $user = new User();
                    $user -> setFirstName("prénom$k")
                    ->setLastName("nom$k")
                    ->setEmail("test$k@test.fr")
                    ->setPicture("https://via.placeholder.com/64")
                    ->setIntroduction("introduction $k")
                    ->setDescription("Description $k")
                    ->setSlug("prenom$k-nom$k")
                    ->setHash($this->passwordEncoder->encodePassword(
                       $user,
                        'password'
                     ));
                   // ->setHash ("password");
                    

                    $manager->persist($user);
                    $manager->flush(); // un flush qui enregistre l'utilisateur

                    //dump($user->getId());
                    $slug2 = $user->getSlug(). '_' .$user->getId(); // faut rentre l'url unique en recuperant l'id de l'utilisateur par rapport a l'annonce 
                    $user->setSlug($slug2);   // si 2 utilisateur ont le mm prenom nom => on recupere dc l'id pour le dupliquer aux nom du user
                    $manager->persist($user);
                 
                     // si on a des  utilisateurs qui possedent le mm prenom nom => on rend dc l'url uniqe ainsi que l'id qui sera en rapport  avc l'annonce  


                    //annonce
                    for ($i=0; $i< mt_rand(1,3); $i++)   // for pour les annonces par auteur

                    {
                                        // a chaque classe creer = ctrl + alt+ i 
                    $slugify = new Slugify();  // pour pouvoir l'utiliser a l'emplacement
                    $title= "Titre de l'annonce n° $i";
                    $slug=$slugify->slugify($title); 
                    
                    $ad = new Ad();
                    
                    $ad->setTitle("Titre de l'annonce n°: $i ")
                    ->setSlug($slug)
                    ->setPrice(mt_rand(40,200))
                    ->setIntroduction("Introduction de <b><i>l'annonce n°: $i</i></b><br>")
                    ->setContent("Contenu de <b>l'annonce n°: $i</b><br>")
                    ->setCoverImage("https://via.placeholder.com/350")
                    ->setRooms(mt_rand(1,5))
                    ->setAuthor($user);


                    for($j=0; $j < mt_rand(1,5);$j++)  // pour les images
                    {
                        $image = new Image();
                        $image->setUrl("https://via.placeholder.com/350");
                        $image->setCaption("légende de l'image $j");
                        $image->setAd($ad);
                        
                        $manager->persist($image);
                    }


                        $manager->persist($ad);
                    //persist garde les données d'avant...il fait une boucle 
                    $manager->flush();

                    //dump($ad->getId());
                    $slug2 = $ad->getSlug(). '_' .$ad->getId(); // stopper l'url dupliquer => on la rend unique => pour que le titre et le title de l'annonce ossede le mm id
                    $ad->setSlug($slug2);    // exple: si une annonce possède le mm titre que le title.
                                    //on a donc associer l'id a l'annonce
                    $manager->persist($ad);
                    $manager->flush();

                }
                // $product = new Product();
                // $manager->persist($product);

                //$manager->flush();  //permet de tt envoyer d'un coup
            }
        }
}