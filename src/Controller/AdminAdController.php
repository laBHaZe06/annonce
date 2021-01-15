<?php

namespace App\Controller;

use App\Repository\AdRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminAdController extends AbstractController
{
    /**
     * @Route("/admin/ads/{page}", name="admin_ads_index",  requirements={"page"="[0-9]{1,}"}))
     */
    public function index(AdRepository $repo, $page=1) //on rentre une variable page qui dit que par défaut ca sera la 1ere page
    {       
            $limit = 5 ; // on veut 10 enregistrement par page
           //$ads = $repo->findAll();  //on interroge le repository
            $start = $limit * $page - $limit; // calcul par page
            $total =count( $repo->findAll());// nbrs total d'annonces 

            $pages = ceil($total/$limit); // ceil permet d'arrondir a l'entier superieure
            //on a donc ici le nombre total de pages arrondi a l'entier superieur

        return $this->render('admin/ad/index.html.twig', [
          'ads' => $repo->findBy([],[],$limit,$start),    // on recupere tte les annonces qu'on renvoit au twig de l'admin
          'page'=>$page,
          'pages'=>$pages
          ]);
    }
}
