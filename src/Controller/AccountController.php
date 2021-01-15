<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use Cocur\Slugify\Slugify;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    /**
     * @Route("/register", name="account_register")
     */
     public function register(EntityManagerInterface $manager,Request $request,UserPasswordEncoderInterface $passwordEncoder){

                $user = new User();

                $form = $this->createForm(RegistrationType::class, $user);
                $form->handleRequest($request); //cela recupere la requete
                //dump($ad);
                            
                if ($form->isSubmitted() && $form->isValid())
                {
                    $password = $user->getHash();
                    $pass_encoded = $passwordEncoder->encodePassword($user, $password);
                    $user->setHash($pass_encoded);

                    $slugify = new Slugify();  
                    $slug = $slugify->slugify($user->getFirstName().'_'.$user->getLastName());
                    $user->setSlug($slug);

                    $manager->persist($user);
                                    //persist garde les données d'avant...il fait une boucle 
                    $manager->flush();

                    $slug = $user->getSlug(). '_' .$user->getId(); // stopper l'url dupliquer
                    $user->setSlug($slug);    // exple: si une annonce possède le mm titre que le title.
                                    //on a donc associer l'id a l'annonce
                    $manager->persist($user);
                    $manager->flush();


                    $this->addFlash(
                'success',
                'Merci' .$user->getFirstName(). 'vous avez bien été enregistré'
                );
           
          

                     return $this->redirectToRoute('account_login');



                
                      
    
        }
            
        return $this->render('account/register.html.twig', [
            'form'=>$form->createView()
                ]);
    }






    //modification du profil

    /**
     * @Route("/account/profil", name="account_profil")
     */
     public function profil (EntityManagerInterface $manager,Request $request){
            
            $user = $this->getUser();
        

            $form = $this->createForm(AccountType::class, $user);
            $form->handleRequest($request); 
            //cela recupere la requete du formulaire
                //dump($ad);
            
            if ($form->isSubmitted() && $form->isValid())
            {



              
              
            $this->addFlash(
            'success',
            'Le Profile de ' .$user->getFirstName(). ' a bien été modifié'
            );
           
          

            return $this->redirectToRoute('account_index');



                
                      
    
    }

            return $this->render('account/profil.html.twig', [
            'form'=>$form->createView(),
            'user'=>$user,

        ]);
     }
    



    /**
     * @Route("/account/index", name="account_index")
     */

    public function myAccount (EntityManagerInterface $manager,Request $request){ 


        
        
        
        
        return $this->render('user/index.html.twig', [
            'user'=>$this->getUser(),   // ici on recupere tt ce qui concerne l'utilisateur 
          ]);
     
    }



    /**
     * @Route("/account/password-update", name="account_password")
     */
    public function updatePassword (EntityManagerInterface $manager,Request $request,UserPasswordEncoderInterface $passwordEncoder){ 
     
                
                $user = $this->getUser();
                $passwordUpdate = new PasswordUpdate();
                

                $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);
                $form->handleRequest($request); 
                
            //dump($ad);
        
        if ($form->isSubmitted() && $form->isValid())
            { 
                if(!password_verify($passwordUpdate->getOldPassword(), $user->getHash()))
                {
                     $this->addFlash(
                        'danger',
                        'L\'ancien mot de passe est incorrect'
                         );
                }else{
                    $newPassword = $passwordUpdate->getNewPassword();
                    $pass_encoded = $passwordEncoder->encodePassword($newPassword);
                    $user->setHash($pass_encoded);


                }

                

            
            }    
           
              
      return $this->render('account/password.html.twig', [
            'form'=>$form->createView(),
            'user'=>$user,
            
          ]);
      }

    

            /**
             * @Route("/login", name="account_login")
             */
            public function login(AuthenticationUtils $utils)
            {

                // get the login error if there is one
                $error = $utils->getLastAuthenticationError(); // vérifie et teste les erreurs
                return $this->render('account/login.html.twig', [
                    'loginError' => $error,
                ]);
            }

            /**
             * @Route("/logout", name="account_logout")
             */
            public function logout()
            {
            
                
            }
}
