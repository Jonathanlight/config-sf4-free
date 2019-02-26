<?php

namespace App\Manager\Api;

use App\Entity\Utilisateur;
use App\Repository\CryptoRepository;
use App\Repository\OperationRepository;
use App\Repository\ParainageRepository;
use App\Repository\UtilisateurRepository;
use App\Services\MailService;
use App\Services\MessageService;
use App\Services\PasswordService;
use Doctrine\ORM\EntityManagerInterface;

class ParainageManager
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
     * @var OperationRepository
     */
    protected $operationRepository;

    /**
     * @var ParainageRepository
     */
    protected $parainageRepository;

    /**
     * @var CryptoRepository
     */
    protected $cryptoRepository;

    /**
     * @param EntityManagerInterface $em
     * @param MailService $mailService
     * @param \Twig_Environment $templating
     * @param MessageService $messageService
     * @param UtilisateurRepository $utilisateurRepository
     * @param OperationRepository $operationRepository
     * @param ParainageRepository $parainageRepository
     * @param CryptoRepository $cryptoRepository
     */
    public function __construct(
        EntityManagerInterface $em,
        MailService $mailService,
        \Twig_Environment $templating,
        MessageService $messageService,
        UtilisateurRepository $utilisateurRepository,
        OperationRepository $operationRepository,
        ParainageRepository $parainageRepository,
        CryptoRepository $cryptoRepository
    ) {
        $this->em = $em;
        $this->mailService = $mailService;
        $this->templating = $templating;
        $this->messageService = $messageService;
        $this->utilisateurRepository = $utilisateurRepository;
        $this->operationRepository = $operationRepository;
        $this->parainageRepository = $parainageRepository;
        $this->cryptoRepository = $cryptoRepository;
    }

    /**
     * @param Utilisateur $utilisateur
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function checkParrainage(Utilisateur $utilisateur)
    {
        $AllCryptos = $this->cryptoRepository->findAll();

        //Verification du lien de parainage
        $AllFilleules = $this->parainageRepository->getCheckParainUser($utilisateur);

        if ($AllFilleules) {
            foreach ($AllFilleules as $filleul) {
                $checkSoldeOperationFilleul = $this->operationRepository->getAllOperationSuccessByUser($filleul->getUtilisateurFilleul());
                if ($checkSoldeOperationFilleul) {
                    if ($checkSoldeOperationFilleul[0]['total'] >= 100) {
                        $parrain_ = $this->parainageRepository->getCheckParainUserFilleul($utilisateur, $filleul->getUtilisateurFilleul());
                        $parrain = $parrain_[0];
                        $parrain->setEtat(1);
                        $this->em->persist($parrain);
                        $this->em->flush();

                        //Calcul pour deduire le nouveau solde parrain
                        $newSoldeCurrent_parrain = $utilisateur->getSolde()->getSolde() + $parrain->getSolde();
                        $newSoldeOld_parrain = $utilisateur->getSolde()->getSoldeOld() + $parrain->getSolde();
                        $utilisateur->getSolde()->setSolde($newSoldeCurrent_parrain);
                        $utilisateur->getSolde()->setSoldeOld($newSoldeOld_parrain);
                        $utilisateur->getSolde()->setUpdated(new \DateTime());
                        $this->em->persist($utilisateur);
                        $this->em->flush();

                        //Calcul pour deduire le nouveau solde filleul
                        $newSoldeCurrent_filleul = $filleul->getUtilisateurFilleul()->getSolde()->getSolde() + $parrain->getSolde();
                        $newSoldeOld_filleul = $filleul->getUtilisateurFilleul()->getSolde()->getSoldeOld() + $parrain->getSolde();
                        $filleul->getUtilisateurFilleul()->getSolde()->setSolde($newSoldeCurrent_filleul);
                        $filleul->getUtilisateurFilleul()->getSolde()->setSoldeOld($newSoldeOld_filleul);
                        $filleul->getUtilisateurFilleul()->getSolde()->setUpdated(new \DateTime());
                        $this->em->persist($filleul->getUtilisateurFilleul());
                        $this->em->flush();

                        $this->mailService->sendMail(
                            'CRYPTIZY - Récompense de parrainage',
                            ['contact@cryptizy.com' => 'CRYPTIZY'],
                            [$utilisateur->getEmail(), $filleul->getUtilisateurFilleul()->getEmail()],
                            $this->templating->render('email/user/transfertUser.html.twig', [])
                        );
                    }
                }
            }
        }
    }
}
