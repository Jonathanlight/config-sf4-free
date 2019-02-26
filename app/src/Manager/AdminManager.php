<?php

namespace App\Manager;

use App\Entity\Admin;
use App\Entity\Article;
use App\Entity\Categorie;
use App\Entity\Depot;
use App\Entity\Utilisateur;
use App\Repository\CategorieRepository;
use App\Repository\DepotRepository;
use App\Repository\OperationRepository;
use App\Repository\RetraitRepository;
use App\Repository\TransfertcryptoRepository;
use App\Repository\UtilisateurRepository;
use App\Repository\VendreRepository;
use App\Services\MailService;
use App\Services\MessageService;
use App\Services\PasswordService;
use App\Services\QueryIpGeolocalisation;
use App\Services\UploadType;
use Doctrine\ORM\EntityManagerInterface;

class AdminManager
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
     * @var UploadType
     */
    protected $uploadType;

    /**
     * @var UtilisateurRepository
     */
    protected $utilisateurRepository;

    /**
     * @var DepotRepository
     */
    protected $depotRepository;

    /**
     * @var CategorieRepository
     */
    protected $categorieRepository;

    /**
     * @var VendreRepository
     */
    protected $vendreRepository;

    /**
     * @var TransfertcryptoRepository
     */
    protected $transfertcryptoRepository;

    /**
     * @var RetraitRepository
     */
    protected $retraitRepository;

    /**
     * @var OperationRepository
     */
    protected $operationRepository;

    /**
     * AdminManager constructor.
     * @param EntityManagerInterface $entityManager
     * @param MailService $mailService
     * @param \Twig_Environment $templating
     * @param PasswordService $passwordService
     * @param MessageService $messageService
     * @param QueryIpGeolocalisation $queryIpGeolocalisation
     * @param UploadType $uploadType
     * @param UtilisateurRepository $utilisateurRepository
     * @param DepotRepository $depotRepository
     * @param CategorieRepository $categorieRepository
     * @param VendreRepository $vendreRepository
     * @param TransfertcryptoRepository $transfertcryptoRepository
     * @param RetraitRepository $retraitRepository
     * @param OperationRepository $operationRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        MailService $mailService,
        \Twig_Environment $templating,
        PasswordService $passwordService,
        MessageService $messageService,
        QueryIpGeolocalisation $queryIpGeolocalisation,
        UploadType $uploadType,
        UtilisateurRepository $utilisateurRepository,
        DepotRepository $depotRepository,
        CategorieRepository $categorieRepository,
        VendreRepository $vendreRepository,
        TransfertcryptoRepository $transfertcryptoRepository,
        RetraitRepository $retraitRepository,
        OperationRepository $operationRepository
    ) {
        $this->em = $entityManager;
        $this->mailService = $mailService;
        $this->templating = $templating;
        $this->passwordService = $passwordService;
        $this->messageService = $messageService;
        $this->queryIpGeolocalisation = $queryIpGeolocalisation;
        $this->uploadType = $uploadType;
        $this->utilisateurRepository = $utilisateurRepository;
        $this->depotRepository = $depotRepository;
        $this->categorieRepository = $categorieRepository;
        $this->vendreRepository = $vendreRepository;
        $this->transfertcryptoRepository = $transfertcryptoRepository;
        $this->retraitRepository = $retraitRepository;
        $this->operationRepository = $operationRepository;
    }

    /**
     * @param array $data
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function confirmerDepot(array $data): void
    {
        $depotClient = $this->depotRepository->find($data['depot_id']);
        $utilisateur = $this->utilisateurRepository->find($data['user_id']);

        $depotClient->setState(1);
        $this->em->persist($depotClient);
        $this->em->flush();

        //Calcul pour deduire le nouveau solde user
        $newSoldeCurrent = $utilisateur->getSolde()->getSolde() + $depotClient->getSolde();
        $newSoldeOld = $utilisateur->getSolde()->getSoldeOld() + $depotClient->getSolde();
        $utilisateur->getSolde()->setSolde($newSoldeCurrent);
        $utilisateur->getSolde()->setSoldeOld($newSoldeOld);
        $this->em->persist($utilisateur);
        $this->em->flush();

        // Envoie du mail de confirmation
        $contentData = [
            'prenom' => $utilisateur->getPrenom()
        ];

        $this->mailService->sendMail(
            'CRYPTIZY - Dépôt confirmé',
            ['contact@cryptizy.com' => 'CRYPTIZY'],
            [$utilisateur->getEmail()],
            $this->templating->render('email/admin/confirmDeposit.html.twig', $contentData)
        );

        $this->messageService->addSuccess('Dépôt confirmé');
    }

    /**
     * @param array $data
     */
    public function disableDepot(array $data): void
    {
        $depotClient = $this->depotRepository->find($data['depot_id']);
        $depotClient->setDisable(1);
        $this->em->persist($depotClient);
        $this->em->flush();

        $this->messageService->addSuccess('Dépôt Annulée');
    }

    /**
     * @return mixed
     */
    public function listUsers()
    {
        return $this->utilisateurRepository->getAllUserByRole();
    }

    /**
     * @param Admin $admin
     * @param array $data
     */
    public function newArticle(Admin $admin, array $data): void
    {
        $extention_ = null;
        if ($_FILES) {
            if (isset($_FILES['image']['name'])) {
                $image = "blog_" . uniqid();
                $pathname = "document/blog";

                if ($this->uploadType->upload($_FILES['image'], $image, $pathname) === true) {
                    if (strstr($_FILES['image']["type"], 'jpg')) {
                        $extention_ = strstr($_FILES['image']["type"], 'jpg');
                    }
                    if (strstr($_FILES['image']["type"], 'png')) {
                        $extention_ = strstr($_FILES['image']["type"], 'png');
                    }
                    if (strstr($_FILES['image']["type"], 'jpeg')) {
                        $extention_ = strstr($_FILES['image']["type"], 'jpeg');
                    }
                    if (strstr($_FILES['image']["type"], 'pdf')) {
                        $extention_ = strstr($_FILES['image']["type"], 'pdf');
                    }
                }

                $Categorie   = $this->categorieRepository->find($data['categorie']);
                $article = new Article();
                $article->setDescription($data['description']);
                $article->setContenu($data['contenu']);
                $article->setTitre($data['titre']);
                $article->setImage($image.'.'.$extention_);
                $article->setCategorie($Categorie);
                $article->setAdmin($admin);
                $article->setEtat(1);
                $article->setUpdated(new \DateTime());
                $article->setCreated(new \DateTime());
                $this->em->persist($article);
                $this->em->flush();
                $this->messageService->addSuccess(' Votre article à bien été créer.');
            }
        }
    }

    /**
     * @param Article $article
     */
    public function activeArticle(Article $article): void
    {
        $article->setEtat(1);
        $article->setUpdated(new \DateTime());
        $this->em->persist($article);
        $this->em->flush();
        $this->messageService->addSuccess(' Votre article à bien été activé.');
    }

    /**
     * @param Article $article
     */
    public function deleteArticle(Article $article): void
    {
        $article->setEtat(0);
        $article->setUpdated(new \DateTime());
        $this->em->persist($article);
        $this->em->flush();
        $this->messageService->addSuccess('Votre article à bien été désactivé.');
    }

    /**
     * @param Article $article
     */
    public function editArticle(Article $article, array $data): void
    {
        $article->setDescription($data['description']);
        $article->setContenu($data['contenu']);
        $article->setTitre($data['titre']);
        $article->setUpdated(new \DateTime());
        $this->em->persist($article);
        $this->em->flush();
        $this->messageService->addSuccess('Votre article à bien été modifier.');
    }

    /**
     * @param array $data
     */
    public function newCategorie(array $data): void
    {
        $categorie = new Categorie();
        $categorie->setNom($data['categorie']);
        $categorie->setCreated(new \DateTime());
        $this->em->persist($categorie);
        $this->em->flush();
        $this->messageService->addSuccess('Cette categorie à bien été créer.');
    }

    /**
     * @param string $token
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function reloadmailverification(string $token): void
    {
        $infoUser   = $this->utilisateurRepository->findBy(['reference'=>$token]);

        if ($infoUser) {
            // Envoie du mail de confirmation
            $this->mailService->sendMail(
                'CRYPTIZY - Vérification de votre compte !',
                ['contact@cryptizy.com' => 'CRYPTIZY'],
                [$infoUser[0]->getEmail()],
                $this->templating->render('email/admin/verificationUser.html.twig', [])
            );

            $this->messageService->addSuccess($infoUser[0]->getEmail().' à bien été informé de votre rélance.');
        }
    }

    /**
     * @param string $token
     * @param $local
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function reloadmail(string $token, $local): void
    {
        $user = $this->utilisateurRepository->findBy(['reference'=>$token]);
        if ($user[0] instanceof  Utilisateur) {
            $contentData = [
                'token' => $token,
                'local' => $local
            ];

            $this->mailService->sendMail(
                'CRYPTIZY - Confirmation de votre compte !',
                ['contact@cryptizy.com' => 'CRYPTIZY'],
                [$user[0]->getEmail()],
                $this->templating->render('email/admin/confirmationMail.html.twig', $contentData)
            );

            $this->messageService->addSuccess($user[0]->getEmail().' à bien été informé de votre rélance.');
        }
    }

    /**
     * @param array $data
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function retrait(array $data): void
    {
        $retrait = $this->retraitRepository->find($data['id']);
        $retrait->setState(1);
        $this->em->persist($retrait);
        $this->em->flush();

        $contentData = [
            'prenom' => $data['prenom']
        ];

        $this->mailService->sendMail(
            'CRYPTIZY - Retrait exécuté.',
            ['contact@cryptizy.com' => 'CRYPTIZY'],
            [$data['email']],
            $this->templating->render('email/admin/confirmRetrait.html.twig', $contentData)
        );

        $this->messageService->addSuccess('Retrait validé');
    }

    /**
     * @param array $data
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function transfert(array $data): void
    {
        $transfertCrypto = $this->transfertcryptoRepository->find($data['id']);
        $transfertCrypto->setFrais($data['frais']);
        $transfertCrypto->setValidated(1);
        $transfertCrypto->setUpdated(new \DateTime());
        $this->em->persist($transfertCrypto);
        $this->em->flush();

        $contentData = [
            'prenom' => $data['prenom'],
            'nom_crypto' => $data['crypto']
        ];

        $this->mailService->sendMail(
            'CRYPTIZY - Transfert exécuté.',
            ['contact@cryptizy.com' => 'CRYPTIZY'],
            [$data['email']],
            $this->templating->render('email/admin/confirmTransfer.html.twig', $contentData)
        );

        $this->messageService->addSuccess('Transfert validé');
    }

    /**
     * @param array $data
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sales(array $data): void
    {
        $vendreCurrent = $this->vendreRepository->find($data['id']);

        if ($vendreCurrent->getStateVente() == 2) {
            //virement
            $priceMarge = 5 + ($data['sellCost'] * $data['pourcentage']);
            $priceSolde = $data['sellCost'] - $priceMarge;

            $vendreCurrent->setSellPrice($data['buyPrice']);
            $vendreCurrent->setSellCost($data['sellCost']);
            $vendreCurrent->setSendVirement($priceSolde);
            $vendreCurrent->setSellPriceMarge($priceMarge);
            $vendreCurrent->setPourcentage($data['pourcentage']);
            $vendreCurrent->setSellDate(new \DateTime($data['sellDate']));
            $vendreCurrent->setState(1);
            $this->em->persist($vendreCurrent);
            $this->em->flush();

            // Envoie du mail de confirmation
            $contentData = [
                'prenom' => $vendreCurrent->getUtilisateur()->getPrenom(),
                'crypto_name' => $vendreCurrent->getCrypto()->getName()
            ];
            //User
            $this->mailService->sendMail(
                'CRYPTIZY - Vente confirmé.',
                ['contact@cryptizy.com' => 'CRYPTIZY'],
                [$vendreCurrent->getUtilisateur()->getEmail()],
                $this->templating->render('email/admin/confirmSell.html.twig', $contentData)
            );
        } else {
            //cryptizy
            $emSoldeUserCurrent = $this->utilisateurRepository->find($data['iduser']);

            $priceMarge = $data['sellCost'] * $data['pourcentage'];
            $priceSolde = $data['sellCost'] - $priceMarge;
            $vendreCurrent->setSellPrice($data['buyPrice']);
            $vendreCurrent->setSellCost($data['sellCost']);
            $vendreCurrent->setAddSolde($priceSolde);
            $vendreCurrent->setSellPriceMarge($priceMarge);
            $vendreCurrent->setPourcentage($data['pourcentage']);
            $vendreCurrent->setSellDate(new \DateTime($data['sellDate']));
            $vendreCurrent->setState(1);
            $this->em->persist($vendreCurrent);
            $this->em->flush();

            //reset solde utilisateur
            $newSoldeCurrent = $emSoldeUserCurrent->getSolde()->getSolde() + $priceSolde;
            $emSoldeUserCurrent->getSolde()->setSolde($newSoldeCurrent);
            $this->em->persist($emSoldeUserCurrent);
            $this->em->flush();

            // Envoie du mail de confirmation
            $contentData = [
                'prenom' => $vendreCurrent->getUtilisateur()->getPrenom(),
                'crypto_name' => $vendreCurrent->getCrypto()->getName()
            ];
            //User
            $this->mailService->sendMail(
                'CRYPTIZY - Vente confirmé',
                ['contact@cryptizy.com' => 'CRYPTIZY'],
                [$vendreCurrent->getUtilisateur()->getEmail()],
                $this->templating->render('email/admin/confirmSell.html.twig', $contentData)
            );
        }
    }

    /**
     * @param Utilisateur $utilisateur
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function validated_user(Utilisateur $utilisateur): void
    {
        $utilisateur->setValidated(1);
        $this->em->persist($utilisateur);
        $this->em->flush();

        // Envoie du mail de confirmation
        $contentData = [
            'prenom' => $utilisateur->getPrenom()
        ];

        $this->mailService->sendMail(
            'CRYPTIZY - Profil confirmé',
            ['contact@cryptizy.com' => 'CRYPTIZY'],
            [$utilisateur->getEmail()],
            $this->templating->render('email/admin/verifiedUser.html.twig', $contentData)
        );

        $this->messageService->addSuccess('Profil vérifié');
    }

    /**
     * @param Utilisateur $utilisateur
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function disable_user(Utilisateur $utilisateur): void
    {
        $utilisateur->setValidated(0);
        $this->em->persist($utilisateur);
        $this->em->flush();

        // Envoie du mail de confirmation
        $contentData = [
            'prenom' => $utilisateur->getPrenom()
        ];

        $this->mailService->sendMail(
            'CRYPTIZY - Profil en examen',
            ['contact@cryptizy.com' => 'CRYPTIZY'],
            [$utilisateur->getEmail()],
            $this->templating->render('email/admin/disableUser.html.twig', $contentData)
        );

        $this->messageService->addSuccess('Profil désactivé');
    }

    /**
     * @param array $data
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function operation(array $data): void
    {
        $operationCurrent = $this->operationRepository->find($data['id']);
        $frais = $data['amount'] * $data['pourcentagefrais'];
        $quantite = $data['amount'] - $frais;

        $operationCurrent->setState(1);
        $operationCurrent->setFrais($frais);
        $operationCurrent->setAmount($data['amount']);
        $operationCurrent->setPourcentageFrais($data['pourcentagefrais']);
        $operationCurrent->setQuantite($quantite);
        $operationCurrent->setBuyPrice($data['buyPrice']);
        $operationCurrent->setExchange($data['exchange']);
        $operationCurrent->setDateBuy(new \DateTime($data['created']));
        $this->em->persist($operationCurrent);
        $this->em->flush();

        //ajouter crypto a l'entié Solde crypto
        $user = $this->utilisateurRepository->find($data['iduser']);
        switch ($data['idcrypto']) {
            case 1:
                //Bitcoin
                $newAmount = $quantite + $user->getSoldecrypto()->getBtcSolde();
                $user->getSoldecrypto()->setBtcSolde($newAmount);
                $this->em->persist($user);
                $this->em->flush();
                break;
            case 2:
                //Bitcoin Cash
                $newAmount = $quantite + $user->getSoldecrypto()->getBchSolde();
                $user->getSoldecrypto()->setBchSolde($newAmount);
                $this->em->persist($user);
                $this->em->flush();
                break;
            case 3:
                //Ethereum
                $newAmount = $quantite + $user->getSoldecrypto()->getEthSolde();
                $user->getSoldecrypto()->setEthSolde($newAmount);
                $this->em->persist($user);
                $this->em->flush();
                break;
            case 4:
                //Ethereum Classic
                $newAmount = $quantite + $user->getSoldecrypto()->getEtcSolde();
                $user->getSoldecrypto()->setEtcSolde($newAmount);
                $this->em->persist($user);
                $this->em->flush();
                break;
            case 5:
                //Litecoin
                $newAmount = $quantite + $user->getSoldecrypto()->getLtcSolde();
                $user->getSoldecrypto()->setLtcSolde($newAmount);
                $this->em->persist($user);
                $this->em->flush();
                break;
            case 6:
                //Zcash
                $newAmount = $quantite + $user->getSoldecrypto()->getZecSolde();
                $user->getSoldecrypto()->setZecSolde($newAmount);
                $this->em->persist($user);
                $this->em->flush();
                break;
            case 7:
                //Ripple
                $newAmount = $quantite + $user->getSoldecrypto()->getXrpSolde();
                $user->getSoldecrypto()->setXrpSolde($newAmount);
                $this->em->persist($user);
                $this->em->flush();
                break;
            case 8:
                //EOS
                $newAmount = $quantite + $user->getSoldecrypto()->getEosSolde();
                $user->getSoldecrypto()->setEosSolde($newAmount);
                $this->em->persist($user);
                $this->em->flush();
                break;
        }

        // Envoie du mail de confirmation
        $contentData = [
            'prenom' => $user->getPrenom()
        ];

        $this->mailService->sendMail(
            'CRYPTIZY - Ordre d\'achat éxécuté',
            ['contact@cryptizy.com' => 'CRYPTIZY'],
            [$user->getEmail()],
            $this->templating->render('email/admin/confirmBuy.html.twig', $contentData)
        );
    }

    /**
     * @param Utilisateur $utilisateur
     * @param array $data
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function newDepot(Utilisateur $utilisateur, array $data): void
    {
        $montant = $data['solde'] - $data['marge'];
        $depot = new Depot();
        $depot->setSolde($data['solde']);
        $depot->setUtilisateur($utilisateur);
        $depot->setMarge($data['marge']);
        $depot->setMontant($montant);
        $depot->setSource('virement');
        $depot->setDisable(0);
        $depot->setState(1);
        $depot->setUpdated(new \DateTime());
        $depot->setCreated(new \DateTime($data['created']));
        $this->em->persist($depot);
        $this->em->flush();

        //Calcul pour deduire le nouveau solde user
        $newSoldeCurrent = $utilisateur->getSolde()->getSolde() + $montant;
        $newSoldeOld = $utilisateur->getSolde()->getSoldeOld() + $montant;
        $utilisateur->getSolde()->setSolde($newSoldeCurrent);
        $utilisateur->getSolde()->setSoldeOld($newSoldeOld);
        $this->em->persist($utilisateur);
        $this->em->flush();

        // Envoie du mail de confirmation
        $contentData = [
            'prenom' => $utilisateur->getPrenom()
        ];

        $this->mailService->sendMail(
            'CRYPTIZY - Dépôt confirmé',
            ['contact@cryptizy.com' => 'CRYPTIZY'],
            [$utilisateur->getEmail()],
            $this->templating->render('email/admin/confirmDeposit.html.twig', $contentData)
        );

        $this->messageService->addSuccess('Dépôt confirmé sur le compte : '.$utilisateur->getEmail().' ');
    }
}
