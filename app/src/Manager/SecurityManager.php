<?php

namespace App\Manager;

use App\Entity\Parainage;
use App\Entity\Solde;
use App\Entity\Soldecrypto;
use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use App\Services\MailService;
use App\Services\MessageService;
use App\Services\PasswordService;
use App\Services\QueryIpGeolocalisation;
use App\Services\TypeEmail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class SecurityManager
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
     * @var PasswordService
     */
    protected $passwordService;

    /**
     * @var MessageService
     */
    protected $messageService;

    /**
     * @var QueryIpGeolocalisation
     */
    protected $queryIpGeolocalisation;

    /**
     * @var TypeEmail
     */
    protected $typeEmail;

    /**
     * @var UtilisateurRepository
     */
    protected $utilisateurRepository;

    /**
     * SecurityManager constructor.
     * @param EntityManagerInterface $entityManager
     * @param MailService $mailService
     * @param \Twig_Environment $templating
     * @param PasswordService $passwordService
     * @param MessageService $messageService
     * @param QueryIpGeolocalisation $queryIpGeolocalisation
     * @param TypeEmail $typeEmail
     * @param UtilisateurRepository $utilisateurRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        MailService $mailService,
        \Twig_Environment $templating,
        PasswordService $passwordService,
        MessageService $messageService,
        QueryIpGeolocalisation $queryIpGeolocalisation,
        TypeEmail $typeEmail,
        UtilisateurRepository $utilisateurRepository
    ) {
        $this->em = $entityManager;
        $this->mailService = $mailService;
        $this->templating = $templating;
        $this->passwordService = $passwordService;
        $this->messageService = $messageService;
        $this->queryIpGeolocalisation = $queryIpGeolocalisation;
        $this->typeEmail = $typeEmail;
        $this->utilisateurRepository = $utilisateurRepository;
    }

    /**
     * @param Utilisateur $user
     * @param array $data
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function editAccount(Utilisateur $user, array $data): void
    {
        if ($user instanceof Utilisateur) {
            $user->setNom($data['nom']);
            $user->setPrenom($data['prenom']);
            $user->setTelephone($data['numero']);
            $user->setVille($data['ville']);
            $user->setAdresse($data['adresse']);
            $user->setCodePostale($data['cp']);
        }

        $this->em->persist($user);
        $this->em->flush();

        //Envoye de mail
        $this->mailService->sendMail(
            'CRYPTIZY - Mise a jour de votre compte',
            ['contact@cryptizy.com' => 'CRYPTIZY'],
            [$user->getEmail()],
            $this->templating->render('email/user/editProfil.html.twig', [
                'created' => new \DateTime(),
                'nom'     => $user->getNom(),
                'prenom'  => $user->getPrenom()
            ])
        );
    }

    /**
     * @param Utilisateur $user
     * @param string $password
     */
    public function editPassword(Utilisateur $user, string $password): void
    {
        if ($user instanceof Utilisateur) {
            $user->setPassword($this->passwordService->encode($user, $password));
            $this->em->flush();
        }
    }

    public function resetPasswordUser($host, Utilisateur $utilisateur, $email)
    {
        $passwordReset = md5(uniqid());
        $path = $host.'/wpassword/'.$passwordReset;

        $user = $this->utilisateurRepository->find($utilisateur->getId());
        $user->setPasswordReset($passwordReset);
        $this->em->flush();

        //Envoye de mail user
        $this->mailService->sendMail(
            'CRYPTIZY - Récuperation du mot de passe !',
            ['contact@cryptizy.com' => 'CRYPTIZY'],
            [$email],
            $this->templating->render('email/user/resetPassword.html.twig', [
                'nom' => $utilisateur->getNom(),
                'prenom' => $utilisateur->getPrenom(),
                'path' => $path
            ])
        );
    }

    /**
     * @param string $email
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function passwordForget(string $email): void
    {
        $user = $this->utilisateurRepository
            ->findBy(['email'=> $email]);

        if ($user[0] instanceof Utilisateur) {
            // Envoie du mail de confirmation
            $contentData = [
                'prenom' => $user[0]->getPrenom(),
                'token' => $user[0]->getNom()
            ];

            //Envoye de mail user
            $this->mailService->sendMail(
                'CRYPTIZY - Récuperation du mot de passe !',
                ['contact@cryptizy.com' => 'CRYPTIZY'],
                [$email],
                $this->templating->render('email/user/resetPassword.html.twig', $contentData)
            );

            $this->messageService->addSuccess('Consultez votre adresse e-mail');
        } else {
            $this->messageService->addError('Votre adresse e-mail est inconnue');
        }
    }

    /**
     * @param string $email
     * @return array
     */
    public function findByEmail(string $email): array
    {
        return $this->utilisateurRepository->findBy(['email'=> $email]);
    }

    /**
     * @param string $token
     * @return array
     */
    public function checkToken(string $token)
    {
        return $this->utilisateurRepository->findBy(["reference"=>$token]);
    }

    /**
     * @param Utilisateur $user
     * @param $password
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function resetPassword(Utilisateur $user, $password): void
    {
        $user->setPassword($this->passwordService->encode($user, $password));
        $user->setPasswordReset(null);
        $user->setPasswordUpdated(new \DateTime());
        $this->em->flush();

        //Envoye de mail user
        $this->mailService->sendMail(
            'CRYPTIZY - Récuperation du mot de passe !',
            ['contact@cryptizy.com' => 'CRYPTIZY'],
            [$user->getEmail()],
            $this->templating->render('email/user/passwordUpate.html.twig', [
                'nom' => $user->getNom(),
                'prenom' => $user->getPrenom(),
            ])
        );
    }

    /**
     * @param Utilisateur $user
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function activeUser(Utilisateur $user): void
    {
        if ($user instanceof Utilisateur) {
            $membre = $this->utilisateurRepository->find($user->getId());
            $membre->setActive(1);

            //Envoye de mail user
            $this->mailService->sendMail(
                'CRYPTIZY - Votre compte à bien été confirmer',
                ['contact@cryptizy.com' => 'CRYPTIZY'],
                [$user->getEmail()],
                $this->templating->render('email/user/validationInscrisMail.html.twig', [
                    'nom' => $user->getNom(),
                    'prenom' => $user->getPrenom()
                ])
            );

            //Envoye de mail admin
            $this->mailService->sendMail(
                'CRYPTIZY - Confirmer user',
                ['contact@cryptizy.com' => 'CRYPTIZY'],
                ['jonathan.kablan@gmail.com','khouiel.salah@gmail.com'],
                $this->templating->render('email/admin/validationInscrisMail.html.twig', [
                    'nom' => $user->getNom(),
                    'prenom' => $user->getPrenom(),
                    'email' => $user->getEmail(),
                    'ref' => $user->getReference()
                ])
            );

            $this->em->flush();
        }
    }

    /**
     * @param string $reference
     * @return Utilisateur|null
     */
    public function parrain(string $reference)
    {
        $referenceParain = $this->utilisateurRepository->findBy(['reference' => $reference]);

        if (!$referenceParain[0] instanceof Utilisateur) {
            return null;
        }

        return $referenceParain[0];
    }

    /**
     * @param string $reference
     * @return mixed
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function enable(string $reference)
    {
        $user = $this->utilisateurRepository->findBy(['reference' => $reference]);

        if ($user[0] instanceof Utilisateur) {
            $user[0]->setActive(1);
            $this->em->flush();

            sleep(2);
            // Envoie du mail de confirmation
            $this->mailService->sendMail(
                'CRYPTIZY - Félicitation votre compte est activé.',
                ['contact@cryptizy.com' => 'CRYPTIZY'],
                [$user[0]->getEmail()],
                $this->templating->render('email/user/welcome.html.twig', ['prenom' => $user[0]->getPrenom()])
            );

            return $this->messageService->addSuccess('Félicitation votre compte est maintenant activé');
        }

        return $this->messageService->addError('votre compte est toujours inactivé');
    }

    /**
     * @param Utilisateur $user
     * @param $hostname
     * @param $ipAdresse
     * @param null $parrain
     * @return mixed
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function registerUtilisateur(Utilisateur $user, $hostname, $ipAdresse, $parrain = null)
    {
        if (!$this->typeEmail->checkFormatEmail($user->getEmail())) {
            return $this->messageService->addError('Votre adresse email n\'est pas conforme a notre procédure d\'inscription.');
        }

        /*if (!$this->passwordService->formatRequirement($user->getPassword())) {
            return $this->messageService->addError('Le format du mot de passe (Miniscule - Majuscule - Chiffre - Symbole)');
        }*/

        if (count($this->utilisateurRepository->findBy(["email"=>$user->getEmail()])) !== 0) {
            return $this->messageService->addError('Cette adresse e-mail est déjà utilisée');
        }

        if ($parrain instanceof Utilisateur) {
            $parainage = new Parainage();
            $parainage->setSolde(10);
            $parainage->setEtat(0);
            $parainage->setUtilisateurParrain($parrain);
            $parainage->setCreated(new \DateTime());
            $this->em->persist($parainage);
        }

        //reference generer
        $ref = strtoupper(uniqid());
        $passwordReset = md5(uniqid());

        $solde = new Solde();
        $solde->setSolde(0);
        $solde->setSoldeOld(0);
        $solde->setUpdated(new \DateTime());

        $soldecrypto = new Soldecrypto();
        $soldecrypto->setBchSolde(0);
        $soldecrypto->setBtcSolde(0);
        $soldecrypto->setEtcSolde(0);
        $soldecrypto->setEthSolde(0);
        $soldecrypto->setLtcSolde(0);
        $soldecrypto->setXrpSolde(0);
        $soldecrypto->setZecSolde(0);
        $soldecrypto->setEosSolde(0);
        $soldecrypto->setUpdated(new \DateTime());

        //$locator = $this->queryIpGeolocalisation->userGeoLocator($ipAdresse);

        $user->setAdresse("");
        $user->setVille("");
        $user->setCodePostale("");
        $user->setCountry("");
        $user->setLatitude("");
        $user->setLongitude("");
        $user->setUsername($user->getEmail());
        $user->setPasswordReset($passwordReset);
        $user->setPasswordUpdated(new \DateTime());
        $user->setRoles(['ROLE_USER']);
        $user->setActive(0);
        $user->setValidated(0);
        $user->setIpadresse($ipAdresse);
        $user->setCgv(1);
        $user->setReference($ref);
        $user->setCreated(new \DateTime());
        $pass = $this->passwordService->encode($user, $user->getPassword());
        $user->setPassword($pass);
        $user->setSolde($solde);
        $user->setSoldecrypto($soldecrypto);

        $this->em->persist($user);
        $this->em->flush();

        // Envoie du mail de confirmation
        $contentData = [
            'token' => $ref,
            'link' => $hostname.'/enable_user/'.$ref,
            'nom'    => $user->getNom(),
            'prenom' => $user->getPrenom(),
            'ref' => $ref,
            'email' => $user->getEmail(),
            'genre' => $user->getGenre(),
            'created' => new \DateTime()
        ];


        //Envoye de mail user
        $this->mailService->sendMail(
            'CRYPTIZY - Confirmation de votre compte !',
            ['contact@cryptizy.com' => 'CRYPTIZY'],
            [$user->getEmail()],
            $this->templating->render('email/user/confirmationMail.html.twig', $contentData)
        );

        //Envoye de mail admin
        $this->mailService->sendMail(
            'CRYPTIZY - Confirmation de votre compte !',
            ['contact@cryptizy.com' => 'CRYPTIZY'],
            ['jonathan.kablan@gmail.com','khouiel.salah@gmail.com'],
            $this->templating->render('email/admin/adminNewUser.html.twig', $contentData)
        );

        return $this->messageService->addSuccess('Vos informations ont bien été prises en compte - Validez votre inscription via votre adresse e-mail');
    }
}
