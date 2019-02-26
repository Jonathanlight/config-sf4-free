<?php

namespace App\Manager;

use App\Entity\Article;
use App\Entity\Commentaire;
use App\Entity\Utilisateur;
use App\Repository\OperationRepository;
use App\Services\MailService;
use App\Services\MessageService;
use Doctrine\ORM\EntityManagerInterface;

class OperationManager
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
     * @var OperationRepository
     */
    protected $operationRepository;

    /**
     * SecurityManager constructor.
     * @param EntityManagerInterface $em
     * @param MailService $mailService
     * @param \Twig_Environment $templating
     * @param MessageService $messageService
     * @param OperationRepository $operationRepository
     */
    public function __construct(
        EntityManagerInterface $em,
        MailService $mailService,
        \Twig_Environment $templating,
        MessageService $messageService,
        OperationRepository $operationRepository
    ) {
        $this->em = $em;
        $this->mailService = $mailService;
        $this->templating = $templating;
        $this->messageService = $messageService;
        $this->operationRepository = $operationRepository;
    }

    /**
     * @return \App\Entity\Operation[]
     */
    public function collection()
    {
        return $this->operationRepository->findAll();
    }

    /**
     * @param Utilisateur $utilisateur
     * @return array|mixed
     */
    public function operationByUser(Utilisateur $utilisateur)
    {
        return $this->operationRepository->getOperationByUser($utilisateur);
    }

    /**
     * @param array $data
     */
    public function addAdresseWallet(array $data)
    {
        $adresseWallet = trim($data['adresseWallet']);
        $operation = $this->operationRepository->find($data['id']);
        $operation->setAdresseWallet($adresseWallet);
        $this->em->persist($operation);
        $this->em->flush();

        $this->messageService->addSuccess('Vente envoyé');
    }
}
