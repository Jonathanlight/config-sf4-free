<?php

namespace App\Manager;

use App\Entity\Article;
use App\Entity\Commentaire;
use App\Entity\Utilisateur;
use App\Repository\AdressewalletRepository;
use App\Repository\OperationRepository;
use App\Services\MailService;
use App\Services\MessageService;
use Doctrine\ORM\EntityManagerInterface;

class AdressewalletManager
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
     * @var AdressewalletRepository
     */
    protected $adressewalletRepository;

    /**
     * SecurityManager constructor.
     * @param EntityManagerInterface $em
     * @param MailService $mailService
     * @param \Twig_Environment $templating
     * @param MessageService $messageService
     * @param AdressewalletRepository $adressewalletRepository
     */
    public function __construct(
        EntityManagerInterface $em,
        MailService $mailService,
        \Twig_Environment $templating,
        MessageService $messageService,
        AdressewalletRepository $adressewalletRepository
    ) {
        $this->em = $em;
        $this->mailService = $mailService;
        $this->templating = $templating;
        $this->messageService = $messageService;
        $this->adressewalletRepository = $adressewalletRepository;
    }

    /**
     * @return \App\Entity\Adressewallet[]
     */
    public function collection()
    {
        return $this->adressewalletRepository->findAll([], ['created' => 'DESC']);
    }

    /**
     * @param Utilisateur $utilisateur
     * @return mixed
     */
    public function adresseWalletByUser(Utilisateur $utilisateur)
    {
        return $this->adressewalletRepository->getAdressewalletByUser($utilisateur);
    }
}
