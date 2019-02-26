<?php

namespace App\Manager\Api;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use App\Services\MailService;
use App\Services\MessageService;
use App\Services\PasswordService;
use Doctrine\ORM\EntityManagerInterface;

class UtilisateurManager
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var MailService
     */
    protected $mailService;

    /**
     * @var \Twig_Environment
     */
    protected $templating;

    /**
     * @var MessageService
     */
    protected $messageService;

    /**
     * @var PasswordService
     */
    protected $passwordService;

    /**
     * @var UtilisateurRepository
     */
    protected $utilisateurRepository;

    /**
     * SecurityManager constructor.
     * @param EntityManagerInterface $em
     * @param MailService $mailService
     * @param \Twig_Environment $templating
     * @param MessageService $messageService
     * @param PasswordService $passwordService
     * @param UtilisateurRepository $utilisateurRepository
     */
    public function __construct(
        EntityManagerInterface $em,
        MailService $mailService,
        \Twig_Environment $templating,
        MessageService $messageService,
        PasswordService $passwordService,
        UtilisateurRepository $utilisateurRepository
    ) {
        $this->em = $em;
        $this->mailService = $mailService;
        $this->templating = $templating;
        $this->messageService = $messageService;
        $this->passwordService = $passwordService;
        $this->utilisateurRepository = $utilisateurRepository;
    }

    /**
     * @return Utilisateur[]
     */
    public function collection()
    {
        return $this->utilisateurRepository->findAll([], ['created' => 'DESC']);
    }

    /**
     * @param array $data
     * @return array
     */
    public function postInscription(array $data)
    {
        $username        = $data['username'];
        $password        = $data['password'];
        $email           = $data['email'];
        $phone           = $data['phone'];
        $daterNow        = new \Datetime();

        if (empty($username) || empty($password) || empty($email)) {
            return ['error' => "Set a value"];
        } else {

            $restresult = $this->utilisateurRepository->findBy(['email'=> $email]);

            if (!$restresult) {
                $user = new Utilisateur();
                $user->setUsername($username);
                $passcrypter = $this->passwordService->encode($user, $password);
                $user->setPassword($passcrypter);
                $user->setEmail($email);
                $user->setCreated($daterNow);
                $user->setRoles(['ROLE_USER']);
                $user->setCgv(1);
                $user->setCreated(new \DateTime());
                $this->em->persist($user);
                $this->em->flush();

                return ['success' => "Utilisateur Added Successfully"];
            } else {
                return ['error' => "Utilisateur already register"];
            }
        }
    }

    /**
     * @param Utilisateur $utilisateur
     * @param array $data
     * @return array
     */
    public function updatePassword(Utilisateur $utilisateur, array $data)
    {
        $password = $data['password'];
        $new_password = $data['new_password'];
        $confirm_new_password = $data['confirm_new_password'];

        if (!empty($password) && !empty($new_password) && !empty($confirm_new_password)) {

            if ($this->passwordService->isValid($utilisateur, $password)) {

                $passcrypter = $this->passwordService->encode($utilisateur, $new_password);
                $utilisateur->setPassword($passcrypter);
                $this->em->persist($utilisateur);
                $this->em->flush();

                $response = ['success' => 'User Updated Successfully'];
            } else {
                $response = ['error' => 'Your password is invalid'];
            }

            
        } else {
            $response = ['error' => 'User Updated Error'];
        }

        return $response;
    }

    /**
     * @param Utilisateur $utilisateur
     * @param array $data
     * @return array
     */
    public function updateProfil(Utilisateur $utilisateur, array $data)
    {
        $nom = $data['nom'];
        $prenom = $data['prenom'];
        $telephone = $data['telephone'];

        $response = ['error' => 'User Updated Error'];

        if (!empty($nom)) {
            $utilisateur->setNom($nom);
            $response = ['success' => 'User profil Updated Successfully'];
            $this->em->persist($utilisateur);
            $this->em->flush();
            
        }

        if (!empty($prenom)) {
            $utilisateur->setPrenom($prenom);
            $response = ['success' => 'User profil Updated Successfully'];
            $this->em->persist($utilisateur);
            $this->em->flush();
            
        }

        if (!empty($telephone)) {
            $utilisateur->setTelephone($telephone);
            $response = ['success' => 'User profil Updated Successfully'];
            $this->em->persist($utilisateur);
            $this->em->flush();
            
        }

        return $response;
    }
}
