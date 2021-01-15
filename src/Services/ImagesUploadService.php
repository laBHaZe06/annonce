<?php


namespace App\Services;


use App\Entity\ImageUpload;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



class ImagesUploadService extends AbstractController
{

 public function upload($ad,$manager)
 {
                foreach ($ad->file as $file) 
                {
                        dump($file->getClientOriginalName()); //on recupere le nom d l'image pour mettre ds le champs
                        // $position_point = strpos($file->getClientOriginalName(),'.'); // supprimé l'extension e l'image grace a strpos et substr
                        //   $original_name=substr($file->getClientOriginalName(),0,$position_point);
                        //le probleme sur la 1ere solution si un point est ds le nom il y aura conflit
                        // voir deuxieme solution ici |
                        //                            V
                        
                    // $original_name = preg_replace('#\.(jpg|png|gif)$#','',$file->getClientOriginalName());
                        //ici expression reguliere on recupere les extension a supprimé
                        // # est le délimiteur... $ est se termine par : ....#^ : commence par..
                    $original_name = preg_replace('#\.[a-zA-Z0-9]*$#','',$file->getClientOriginalName());
                    //ici on supprime les caractere de A-Z minuscule ou Maj et chiffre  0-9
                        $fileName = md5(uniqid('',true)).'.'.$file->guessExtension();           
                        //dump($original_name);
                    
                        $upload=new ImageUpload();
                        $upload->setAd($ad)
                        ->setName($original_name)
                        ->setUrl('/uploads/'.$fileName);
                        
                        $manager->persist($upload);
                        $file->move($this->getParameter('images_directory'),$fileName);
                    }
 }


}