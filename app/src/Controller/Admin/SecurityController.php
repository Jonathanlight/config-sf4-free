<?php

namespace App\Controller\Admin;

use App\Form\Security\AdminType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/admin_xgt45e8/login", name="admin_login", methods={"GET", "POST"})
     * @param AuthenticationUtils $authUtils
     * @return Response
     */
    public function admin(AuthenticationUtils $authUtils): Response
    {
        $form = $this->createForm(AdminType::class, [
            '_username' => $authUtils->getLastUsername(),
        ]);

        return $this->render('security/login_admin.html.twig', [
            'error' => $authUtils->getLastAuthenticationError(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin_xgt45e8/logout", name="logout_admin")
     */
    public function logout()
    {
    }
}
