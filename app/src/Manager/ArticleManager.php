<?php

namespace App\Manager;

use App\Entity\Article;
use App\Entity\Commentaire;
use App\Entity\Utilisateur;
use App\Services\MailService;
use App\Services\MessageService;
use Doctrine\ORM\EntityManagerInterface;

class ArticleManager
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
     * SecurityManager constructor.
     * @param EntityManagerInterface $em
     * @param MailService $mailService
     * @param \Twig_Environment $templating
     * @param MessageService $messageService
     */
    public function __construct(
        EntityManagerInterface $em,
        MailService $mailService,
        \Twig_Environment $templating,
        MessageService $messageService
    ) {
        $this->em = $em;
        $this->mailService = $mailService;
        $this->templating = $templating;
        $this->messageService = $messageService;
    }

    /**
     * @param Article $article
     * @param Utilisateur $user
     * @param array $data
     */
    public function newComment(Article $article, Utilisateur $user, array $data): void
    {
        $contenu = trim($data['contenu']);
        $commentaire = new Commentaire();
        $commentaire->setArticle($article);
        $commentaire->setUtilisateur($user);
        $commentaire->setContenu($contenu);
        $commentaire->setCreated(new \DateTime());
        $this->em->persist($commentaire);
        $this->em->flush();
    }
}
