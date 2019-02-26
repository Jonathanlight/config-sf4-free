<?php

namespace App\Manager;

use App\Entity\Article;
use App\Entity\Commentaire;
use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use App\Services\MailService;
use App\Services\MessageService;
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
     * @var UtilisateurRepository
     */
    protected $utilisateurRepository;

    /**
     * SecurityManager constructor.
     * @param EntityManagerInterface $em
     * @param MailService $mailService
     * @param \Twig_Environment $templating
     * @param MessageService $messageService
     * @param UtilisateurRepository $utilisateurRepository
     */
    public function __construct(
        EntityManagerInterface $em,
        MailService $mailService,
        \Twig_Environment $templating,
        MessageService $messageService,
        UtilisateurRepository $utilisateurRepository
    ) {
        $this->em = $em;
        $this->mailService = $mailService;
        $this->templating = $templating;
        $this->messageService = $messageService;
        $this->utilisateurRepository = $utilisateurRepository;
    }

    /**
     * @return Utilisateur[]
     */
    public function collection()
    {
        return $this->utilisateurRepository->findAll([], ['created' => 'DESC']);
    }
}
