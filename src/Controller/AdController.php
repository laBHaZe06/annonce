<?php

namespace App\Controller;


use App\Entity\Ad;
use App\Entity\Image;
use App\Form\AnnonceType;
use Cocur\Slugify\Slugify;
use App\Entity\ImageUpload;
use App\Repository\AdRepository;
use App\Services\ImagesUploadService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class AdController extends AbstractController
{      // le param converter converti en une annonce...il va rechercher
    /**
     * @Route("/ads", name="ads_index")   // <= ceci est une annotation
     */
    public function index(AdRepository $repo)
    {
       //$repo = $this->getDoctrine()->getRepository(Ad::class); // on va chercher le repository de Ad
       $ads = $repo->findAll(); //prend tous les renseignements de la table visée
       
       //dump($ads);




        return $this->render('ad/index.html.twig', [
            'ads' => $ads,  // je transmet a twig une clé ads  qui contient $ads
        ]);
    }

    /**
     * 
     * @Route("/ads/new", name="ads_create")
     * @IsGranted("ROLE_USER")  
     */
    public function create(EntityManagerInterface $manager,Request $request,ImagesUploadService $upload)
    {

           $ad = new Ad();
              //dump($ad);
           //dump($ad);
          $ad->setAuthor($this->getUser());
        
           $form = $this->createForm(AnnonceType::class, $ad);//createForm creer un formulaire
           $form->handleRequest($request); //handleRequest permet d'extraire les données de $requete et les met en concordance avec $ad
           // cela extrait les données et va les rattacher a l'entité AD
              //les requete vont se mettre dans la var $ad 
               // dump($ad);   verifions si l'objet est rempli



if ($form->isSubmitted() && $form->isValid())
{
            $slugify = new Slugify();  // formate un texte -les caractere speciaux-
            
            $slug=$slugify->slugify($ad->getTitle());
            //dump($slug); 
            $ad->setSlug($slug);

           //dump($ad->getImages());

           //dump($ad);exit;

           foreach ($ad->getImages() as $image) //on recupere chaque image (tableau). l'image etant objet
           {

             $image->setAd($ad);
             $manager->persist($image); //on rattache l'image a l'annonce $ad
              // dump($image);
           }
           

           //gestion des images uploadées
           $upload->upload($ad,$manager);
         
            
            $manager->persist($ad);
            $manager->flush();

            

            $slug2 =$ad->getSlug(). ' ' .$ad->getId();
            $ad->setSlug($slug2);
            
             
           
            
            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
            'success',
            'l\'annonce de titre ' .$ad->getTitle(). 'a bien été enregistrée'
            );
           
          

            return $this->redirectToRoute('ads_show',['slug'=>$ad->getSlug()]);
}
        return $this->render('ad/new.html.twig', [
            'form'=>$form->createView()
        ]);
    }


    /**
     * @Route("/ads/{slug}/edit", name="ads_edit")
     * @Security("is_granted('ROLE_USER') and user == ad.getAuthor()", message="cette annonce ne vous appartient pas")       grace a security et granted => le user est le mm que l'author -- si l'annonce n'est pas celle du user / author alors le msg apparait
     */
    public function edit(EntityManagerInterface $manager,Request $request,Ad $ad,ImagesUploadService $upload)
    {


           $form = $this->createForm(AnnonceType::class, $ad);//createForm creer un formulaire
           $form->handleRequest($request); //handleRequest permet d'extraire les données de $requete et les met en concordance avec $ad
           // cela extrait les données et va les rattacher a l'entité AD
              //les requete vont se mettre dans la var $ad 
               // dump($ad);   verifions si l'objet est rempli



if ($form->isSubmitted() && $form->isValid())
{

             //gestion des images uploadées
           $upload->upload($ad,$manager);
           
           
           //suppression des images uploadées
           $tabId = $ad->tableau_id;               //propriété public on px dc le recuperer tel quel
           $tabId = preg_replace('#^,#','',$tabId);
           // dump($tabID);

           $tabId = explode(',',$tabId); //explode retourne une chaine de caratere 
           //du coup on recupere les id ds un tableau
            foreach ($tabId as $id ) { 
           foreach($ad->getImageUploads() as $image){ //on boucle ->propriete getter (private) collection= tableau dc on boucle
                //dump($image);
                if($id == $image->getID()) //si l'id  est le mm que celui de l'image 
                {
                  $manager->remove($image); // on supprime l'image
                  $manager->flush();

                  unlink($_SERVER['DOCUMENT_ROOT'].$image->getUrl());       // supprimer un fichier
                
                }
           }
        }
           //dump($_SERVER);exit;

            $slugify = new Slugify();  // formate un texte -les caractere speciaux-
            
            $slug=$slugify->slugify($ad->getTitle());
            //dump($slug); 
            $ad->setSlug($slug);

           //dump($ad->getImages());

          // dump($ad);exit;  //on peux voir l'id de la photo via le dump


           foreach ($ad->getImages() as $image) //on recupere chaque image (tableau). l'image etant objet
           {

             $image->setAd($ad);
             $manager->persist($image); //on rattache l'image a l'annonce $ad
              // dump($image);
           }

            $manager->persist($ad);
            $manager->flush();

            

            $slug2 =$ad->getSlug(). ' ' .$ad->getId();
            $ad->setSlug($slug2);
            
             
           
            
            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
            'success',
            'l\'annonce de titre ' .$ad->getTitle(). 'a bien été modifié'
            );
           
          

            return $this->redirectToRoute('ads_show',['slug'=>$ad->getSlug()]);
}
        
return $this->render('ad/edit.html.twig', [
            'form'=>$form->createView(),
            'ad'=>$ad,
            ''
        ]);
    }

            

    /**
     * @Route("/ads/{slug}", name="ads_show")  
     */
    public function show(Ad $ad ) // (AdRepository $repo,$slug)
    // ici l'utilisation d'un Param Converter l'objet AD est interrogé pour trouvé le champs slug
    {
            // findOneBy va chercher la colonne de la bdd , et le parametre est celui de l'url 
       //$ad = $repo->findOneBySlug($slug);
     
     // dump($ad);

        return $this->render('ad/show.html.twig', [
            'ad'=>$ad,
            
        ]);

    }
        /**
         * @Route("/ads{slug}/delete", name="ads_delete")
         * @Security("is_granted('ROLE_USER') and user == ad.getAuthor()", message="Vous ne pouvez pas supprimé cette annonce !")
         */

         public function delete(EntityManagerInterface $manager,Ad $ad)
         {
            $manager->remove($ad);
            $manager->flush();

             $this->addFlash(
            'success',
            'l\'annonce de titre '  .$ad->getTitle().  'a bien été supprimé');
            return $this->redirectToRoute('ads_index');
         }
}
   

