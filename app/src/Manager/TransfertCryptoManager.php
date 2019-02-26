<?php

namespace App\Manager;

use App\Entity\Adressewallet;
use App\Entity\Article;
use App\Entity\Commentaire;
use App\Entity\Crypto;
use App\Entity\Depot;
use App\Entity\Friends;
use App\Entity\Operation;
use App\Entity\Parainage;
use App\Entity\Soldecrypto;
use App\Entity\Transfertcrypto;
use App\Entity\Transfertfriend;
use App\Entity\Utilisateur;
use App\Entity\Vendre;
use App\Repository\TransfertcryptoRepository;
use App\Services\MailService;
use App\Services\MessageService;
use App\Services\PasswordService;
use App\Services\QueryCurl;
use Doctrine\ORM\EntityManagerInterface;

class TransfertCryptoManager
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
     * @var QueryCurl
     */
    protected $queryCurl;

    /**
     * @var PasswordService
     */
    protected $passwordService;

    /**
     * @var TransfertcryptoRepository
     */
    protected $transfertcryptoRepository;

    /**
     * TransfertCryptoManager constructor.
     * @param EntityManagerInterface $em
     * @param MailService $mailService
     * @param \Twig_Environment $templating
     * @param MessageService $messageService
     * @param QueryCurl $queryCurl
     * @param PasswordService $passwordService
     * @param TransfertcryptoRepository $transfertcryptoRepository
     */
    public function __construct(
        EntityManagerInterface $em,
        MailService $mailService,
        \Twig_Environment $templating,
        MessageService $messageService,
        QueryCurl $queryCurl,
        PasswordService $passwordService,
        TransfertcryptoRepository $transfertcryptoRepository
    ) {
        $this->em = $em;
        $this->mailService = $mailService;
        $this->templating = $templating;
        $this->messageService = $messageService;
        $this->queryCurl = $queryCurl;
        $this->passwordService = $passwordService;
        $this->transfertcryptoRepository = $transfertcryptoRepository;
    }

    /**
     * @return array
     */
    public function collection()
    {
        return $this->transfertcryptoRepository->collection();
    }

    /**
     * @param Transfertcrypto $transfertcrypto
     */
    public function delete(Transfertcrypto $transfertcrypto): void
    {
        $transfertcrypto->setDeletedAt(new \DateTime());
        $this->em->persist($transfertcrypto);
        $this->em->flush();
    }
}
