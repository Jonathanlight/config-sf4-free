<?php

namespace App\Manager;

use App\Entity\Utilisateur;
use App\Repository\VendreRepository;
use App\Services\MailService;
use App\Services\MessageService;
use Doctrine\ORM\EntityManagerInterface;

class SellManager
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
     * @var VendreRepository
     */
    protected $vendreRepository;

    /**
     * SellManager constructor.
     * @param EntityManagerInterface $em
     * @param MailService $mailService
     * @param \Twig_Environment $templating
     * @param MessageService $messageService
     * @param VendreRepository $vendreRepository
     */
    public function __construct(
        EntityManagerInterface $em,
        MailService $mailService,
        \Twig_Environment $templating,
        MessageService $messageService,
        VendreRepository $vendreRepository
    ) {
        $this->em = $em;
        $this->mailService = $mailService;
        $this->templating = $templating;
        $this->messageService = $messageService;
        $this->vendreRepository = $vendreRepository;
    }

    /**
     * @param Utilisateur $utilisateur
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getAllSumVirementByUser(Utilisateur $utilisateur)
    {
        return $this->vendreRepository->findBySumAllVirement($utilisateur);
    }
}
