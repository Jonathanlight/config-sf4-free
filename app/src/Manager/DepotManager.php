<?php

namespace App\Manager;

use App\Entity\Article;
use App\Entity\Commentaire;
use App\Entity\Utilisateur;
use App\Repository\DepotRepository;
use App\Services\MailService;
use App\Services\MessageService;
use Doctrine\ORM\EntityManagerInterface;

class DepotManager
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
     * @var DepotRepository
     */
    protected $depotRepository;

    /**
     * SecurityManager constructor.
     * @param EntityManagerInterface $em
     * @param MailService $mailService
     * @param \Twig_Environment $templating
     * @param MessageService $messageService
     * @param DepotRepository $depotRepository
     */
    public function __construct(
        EntityManagerInterface $em,
        MailService $mailService,
        \Twig_Environment $templating,
        MessageService $messageService,
        DepotRepository $depotRepository
    ) {
        $this->em = $em;
        $this->mailService = $mailService;
        $this->templating = $templating;
        $this->messageService = $messageService;
        $this->depotRepository = $depotRepository;
    }

    /**
     * @return mixed
     */
    public function collection()
    {
        return $this->depotRepository->findAll([], ['created' => 'DESC']);
    }

    /**
     * @param Utilisateur $utilisateur
     * @return mixed
     */
    public function depotByUser(Utilisateur $utilisateur)
    {
        return $this->depotRepository->getDepotByUser($utilisateur);
    }
}
