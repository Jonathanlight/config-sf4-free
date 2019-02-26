<?php

namespace App\Controller\Security;

use App\Entity\Utilisateur;
use App\Form\Security\LoginType;
use App\Form\Security\RegisterType;
use App\Manager\SecurityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/user/login/{_locale}", name="login", methods={"GET", "POST"}, defaults={"_locale" = "fr"})
     * @param AuthenticationUtils $authUtils
     * @return Response
     */
    public function user(AuthenticationUtils $authUtils): Response
    {
        if ($this->getUser() instanceof Utilisateur) {
           return $this->redirectToRoute('dashboard_user');
        }

        $form = $this->createForm(LoginType::class, [
            '_username' => $authUtils->getLastUsername(),
        ]);

        return $this->render('security/login.html.twig', [
            'error' => $authUtils->getLastAuthenticationError(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/register/{_locale}", name="register", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     * @param Request $request
     * @param SecurityManager $securityManager
     * @return Response
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function registerUser(
        Request $request,
        SecurityManager $securityManager
    ) {
        $user = new Utilisateur();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($request->getHost() != 'localhost') {
                $hostname = 'https://cryptizy.com/';
            } else {
                $hostname = 'http://'.$request->getHttpHost();
            }
            $securityManager->registerUtilisateur($user, $hostname, $request->server->get('REMOTE_ADDR'));
        }

        return $this->render('security/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/enable/user/{token}/{_locale}", name="enable_user", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     * @param SecurityManager $securityManager
     * @param $token
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function enable(SecurityManager $securityManager, $token)
    {
        $securityManager->enable($token);

        return $this->redirectToRoute('login');
    }

    /**
     * @Route("/register/{ref}/{_locale}", name="insertUser", defaults={"ref" = null}, methods={"GET","POST"}, defaults={"_locale" = "fr"})
     * @param Request $request
     * @param SecurityManager $securityManager
     * @param $ref
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function registerWithParainage(Request $request, SecurityManager $securityManager, $ref)
    {
        $userParrain = null;
        if ($securityManager->parrain($ref) == false) {
            return $this->redirectToRoute('login');
        }

        $user = new Utilisateur();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);

        $userParrain = $securityManager->parrain($ref);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($request->getHost() != 'localhost') {
                $hostname = 'https://cryptizy.com/';
            } else {
                $hostname = 'http://'.$request->getHttpHost();
            }

            $securityManager->registerUtilisateur($user, $hostname, $request->server->get('REMOTE_ADDR'), $userParrain);
        }

        return $this->render('security/registerParainage.html.twig', [
            'form' => $form->createView(),
            "parrain" => $userParrain->getPrenom()
        ]);
    }

    /**
     * @Route("/reset/password/{_locale}", name="resetPassword", methods={"GET"}, defaults={"_locale" = "fr"})
     * @return Response
     */
    public function resetPassword(): Response
    {
        return $this->render('public/resetPassword.html.twig');
    }

    /**
     * @Route("/password/forget/{_locale}", name="passforget", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     * @param Request $request
     * @param SecurityManager $securityManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function passforget(Request $request, SecurityManager $securityManager)
    {
        $securityManager->passwordForget($request->get('emails'));

        return $this->redirectToRoute('login');
    }

    /**
     * @Route("/passwords/{_locale}", name="passwords", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function passwords(Request $request)
    {
        $om = $this->getDoctrine()->getManager();
        $identifient = trim($request->get("tokken"));

        $session = new Session();
        $memorys = $session->set('tokken', $identifient);
        $memory_tokens = $session->get('tokken');

        $traitement = $om->getRepository(Utilisateur::class)->findOneBy(["passwordReset" => $memory_tokens ]);

        if ($traitement) {
            $password1 = $request->get('password_new');
            $password2 = $request->get('password_new2');

            if ($password1 === $password2) {
                $token = $request->get("tokken");
                $passwordReset = md5(uniqid());
                $ids = $traitement->getId();
                $em = $this->getDoctrine()->getManager();
                $update = $em->getRepository(Utilisateur::class)->find($ids);
                $encoders = $this->container->get('security.password_encoder');
                $pass2 = $encoders->encodePassword($update, $password1);
                $update->setPassword($pass2);
                $update->setPasswordReset($passwordReset);
                $update->setPasswordUpdated(new \DateTime());
                $em->flush();
                $request->getSession()->getFlashBag()->add('success', 'Mot de passe modifier');
                return $this->redirectToRoute('login');
            } else {
                $request->getSession()->getFlashBag()->add('error', 'Mot de passe non modifier');
                return $this->redirectToRoute('login');
            }
        } else {
            return $this->redirectToRoute('login');
        }
    }

    /**
     * @Route("/passwords/update/{token}/{_locale}", name="passwords_update", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function passwords2(Request $request)
    {
        $token = $request->get('token');

        if ($token) {
            $om = $this->getDoctrine()->getManager();
            $traitement = $om->getRepository(Utilisateur::class)->findOneBy(["passwordReset" => $token ]);
            if ($traitement) {
                $session = new Session();
                $memory = $session->set('token', $token);
                $memory_token = $session->get('token');
                return $this->render('public/new_pass.html.twig', ['token'=>$memory_token]);
            } else {
                return $this->redirectToRoute('login');
            }
        } else {
            return $this->redirectToRoute('login');
        }
    }

    /**
     * @Route("/user/logout", name="logout_user")
     */
    public function logout()
    {
    }
}
