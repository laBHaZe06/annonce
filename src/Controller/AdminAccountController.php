<?php

namespace App\Controller;

use App\Controller\AdminAccountController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AdminAccountController extends AbstractController
{
    /**
     * @Route("/admin/login", name="admin_account_login")
     *
    **/
    public function login(AuthenticationUtils $utils)
    {

       
                $error = $utils->getLastAuthenticationError(); // vérifie et teste les erreurs de login
                return $this->render('admin/account/login.html.twig', [
                    'loginError' => $error,
                ]);
            }

            /**
             * @Route("/admin/logout", name="admin_account_logout")
             */
            public function logout()
            {
            
                
            }
}
