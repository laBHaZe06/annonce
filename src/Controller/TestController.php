<?php

namespace App\Controller;


use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class TestController extends AbstractController
{
    /**
     * @Route("/test", name="hom")     
     */
    public function index()
    {
        $a='yohan';
       
        $tab=['yohan'=>35,'Gerald'=>53,'floriant'=>26];
        

        dump($tab);
        dump($a);
        dump($this);

        return $this->render('test/test.html.twig', [
           'tableau'=>$tab, 'prenom'=>$a,
           'age'=>13,
        ]);
    }
}
