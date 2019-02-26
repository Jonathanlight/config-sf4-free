<?php

namespace App\Manager;

use App\Entity\Adressewallet;
use App\Entity\Crypto;
use App\Entity\Depot;
use App\Entity\Friends;
use App\Entity\Operation;
use App\Entity\Retrait;
use App\Entity\Transfertcrypto;
use App\Entity\Transfertfriend;
use App\Entity\Utilisateur;
use App\Entity\Vendre;
use App\Repository\CryptoRepository;
use App\Repository\FriendsRepository;
use App\Repository\OperationRepository;
use App\Repository\ParainageRepository;
use App\Services\MailService;
use App\Services\MessageService;
use App\Services\PasswordService;
use App\Services\QueryCurl;
use Doctrine\ORM\EntityManagerInterface;

class CryptoManager
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
     * @var CryptoRepository
     */
    protected $cryptoRepository;

    /**
     * @var FriendsRepository
     */
    protected $friendsRepository;

    /**
     * @var OperationRepository
     */
    protected $operationRepository;

    /**
     * @var ParainageRepository
     */
    protected $parainageRepository;

    /**
     * @param EntityManagerInterface $em
     * @param MailService $mailService
     * @param \Twig_Environment $templating
     * @param MessageService $messageService
     * @param QueryCurl $queryCurl
     * @param PasswordService $passwordService
     * @param CryptoRepository $cryptoRepository
     * @param FriendsRepository $friendsRepository
     * @param OperationRepository $operationRepository
     * @param ParainageRepository $parainageRepository
     */
    public function __construct(
        EntityManagerInterface $em,
        MailService $mailService,
        \Twig_Environment $templating,
        MessageService $messageService,
        QueryCurl $queryCurl,
        PasswordService $passwordService,
        CryptoRepository $cryptoRepository,
        FriendsRepository $friendsRepository,
        OperationRepository $operationRepository,
        ParainageRepository $parainageRepository
    ) {
        $this->em = $em;
        $this->mailService = $mailService;
        $this->templating = $templating;
        $this->messageService = $messageService;
        $this->queryCurl = $queryCurl;
        $this->passwordService = $passwordService;
        $this->cryptoRepository = $cryptoRepository;
        $this->friendsRepository = $friendsRepository;
        $this->operationRepository = $operationRepository;
        $this->parainageRepository = $parainageRepository;
    }

    /**
     * @param Utilisateur $user
     * @param array $data
     */
    public function editTelephone(Utilisateur $user, array $data): void
    {
        $user->setTelephone(trim($data['telephone']));
        $this->em->persist($user);
        $this->em->flush();

        $this->messageService->addSuccess('Compte mis à jour');
    }

    public function errorTransfert($message)
    {
        $this->messageService->addError($message);
    }

    /**
     * @return array|object[]
     */
    public function allCryptos()
    {
        return $this->cryptoRepository->findAll();
    }

    /**
     * @param int $id
     * @return Crypto|null
     */
    public function findCrypto(int $id): Crypto
    {
        return $this->cryptoRepository->find($id);
    }

    /**
     * @param Utilisateur $user
     * @param string $montant
     * @param Utilisateur $FriendUser
     * @param Crypto $cryptoChoose
     */
    public function transfertFriend(Utilisateur $user, string $montant, Utilisateur $FriendUser, Crypto $cryptoChoose): void
    {
        $transfertFriend = new Transfertfriend();
        $transfertFriend->setFrais(0.02);
        $transfertFriend->setAmount($montant);
        $transfertFriend->setState(1);
        $transfertFriend->setValidated(0);
        $transfertFriend->setUpdated(new \DateTime());
        $transfertFriend->setCreated(new \DateTime());
        $transfertFriend->setUtilisateur($user);
        $transfertFriend->setUtilisateurFriend($FriendUser);
        $transfertFriend->setCrypto($cryptoChoose);
        $this->em->persist($transfertFriend);
        $this->em->flush();
    }

    /**
     * @param Utilisateur $user
     * @param Crypto $CryptoAdd
     * @param $stateVente
     * @param $crypto
     */
    public function sellCrypto(Utilisateur $user, Crypto $CryptoAdd, $stateVente, $crypto): void
    {
        $vendre = new Vendre();
        $vendre->setUtilisateur($user);
        $vendre->setCrypto($CryptoAdd);
        $vendre->setState(0);
        $vendre->setAddSolde(0);
        $vendre->setSellPrice(0);
        $vendre->setSellCost(0);
        $vendre->setSellPriceMarge(0);
        $vendre->setPourcentage(0);
        $vendre->setSendVirement(0);
        $vendre->setStateVente($stateVente);
        $vendre->setSellAmount($crypto);
        $vendre->setCreated(new \DateTime());
        $this->em->persist($vendre);
        $this->em->flush();
    }

    /**
     * @param Utilisateur $user
     * @param float $soldeCurrent
     * @param float $solde
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function retrait(Utilisateur $user,float $soldeCurrent,float $solde): void
    {
        if ($soldeCurrent >= $solde) {
            if ($user->getIban() !== null && $user->getBic() !== null) {
                $retrait = new Retrait();
                $retrait->setState(0);
                $retrait->setMarge(Retrait::MARGE_CRYPTIZY);
                $retrait->setSolde($solde);
                $retrait->setMontant($solde - Retrait::MARGE_CRYPTIZY);
                $retrait->setUpdated(new \DateTime());
                $retrait->setCreated(new \DateTime());
                $retrait->setUtilisateur($user);
                $this->em->persist($retrait);
                $this->em->flush();

                //debiter le solde courent
                $newSolde = $soldeCurrent - $solde;

                $user->getSolde()->setSolde($newSolde);
                $this->em->persist($user);
                $this->em->flush();

                // Envoie du mail de confirmation
                $contentData = [
                    'nom' => $user->getNom(),
                    'prenom' => $user->getPrenom()
                ];
                //Admin
                $this->mailService->sendMail(
                    'CRYPTIZY - Demande de Retrait : '.$user->getEmail().'!',
                    ['contact@cryptizy.com' => 'CRYPTIZY'],
                    ["jonathan.kablan@gmail.com", "khouiel.salah@gmail.com"],
                    $this->templating->render('email/admin/adminRetrait.html.twig', $contentData)
                );

                //User
                $this->mailService->sendMail(
                    'CRYPTIZY - Traitement de Retrait ',
                    ['contact@cryptizy.com' => 'CRYPTIZY'],
                    [$user->getEmail()],
                    $this->templating->render('email/user/retraitUser.html.twig', $contentData)
                );

                $this->messageService->addSuccess('Retrait ajouté');
            } else {
                $this->messageService->addError('Pensez à ajouter votre RIB et BIC avant merci ^^ .');
            }
        } else {
            $this->messageService->addError('Votre solde est insuffisant pour cette opération.');
        }
    }

    /**
     * @param Utilisateur $user
     * @param $cryptoCurrent
     * @param $montant
     * @param $setWallet
     * @param Crypto $crypto
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function transfertCrypto(Utilisateur $user, $cryptoCurrent, $montant, $setWallet, Crypto $crypto): void
    {
        if ($montant <= $cryptoCurrent) {
            //reference generer
            $transfertCrypto = new Transfertcrypto();
            $transfertCrypto->setFrais(0);
            $transfertCrypto->setAmount($montant);
            $transfertCrypto->setState(1);
            $transfertCrypto->setValidated(0);
            $transfertCrypto->setUpdated(new \DateTime());
            $transfertCrypto->setCreated(new \DateTime());
            $transfertCrypto->setUtilisateur($user);
            $transfertCrypto->setAdressewallet($setWallet);
            $transfertCrypto->setCrypto($crypto);
            $this->em->persist($transfertCrypto);
            $this->em->flush();

            //***************************************************
            $newSoldeCrypto = $cryptoCurrent - $montant;

            switch ($crypto->getId()) {
                case 1:
                    if ($user instanceof Utilisateur) {
                        $user->getSoldecrypto()->setBtcSolde($newSoldeCrypto);
                        $this->em->persist($user);
                        $this->em->flush();
                    }
                    break;
                case 2:
                    if ($user instanceof Utilisateur) {
                        $user->getSoldecrypto()->setBchSolde($newSoldeCrypto);
                        $this->em->persist($user);
                        $this->em->flush();
                    }
                    break;
                case 3:
                    if ($user instanceof Utilisateur) {
                        $user->getSoldecrypto()->setEthSolde($newSoldeCrypto);
                        $this->em->persist($user);
                        $this->em->flush();
                    }
                    break;
                case 4:
                    if ($user instanceof Utilisateur) {
                        $user->getSoldecrypto()->setEtcSolde($newSoldeCrypto);
                        $this->em->persist($user);
                        $this->em->flush();
                    }
                    break;
                case 5:
                    if ($user instanceof Utilisateur) {
                        $user->getSoldecrypto()->setLtcSolde($newSoldeCrypto);
                        $this->em->persist($user);
                        $this->em->flush();
                    }
                    break;
                case 6:
                    if ($user instanceof Utilisateur) {
                        $user->getSoldecrypto()->setZecSolde($newSoldeCrypto);
                        $this->em->persist($user);
                        $this->em->flush();
                    }
                    break;
                case 7:
                    if ($user instanceof Utilisateur) {
                        $user->getSoldecrypto()->setXrpSolde($newSoldeCrypto);
                        $this->em->persist($user);
                        $this->em->flush();
                    }
                    break;
                case 8:
                    if ($user instanceof Utilisateur) {
                        $user->getSoldecrypto()->setEosSolde($newSoldeCrypto);
                        $this->em->persist($user);
                        $this->em->flush();
                    }
                    break;
            }

            // Envoie du mail de confirmation
            $contentData = [
                'nom' => $user->getNom(),
                'prenom' => $user->getPrenom(),
                'nom_crypto' => $crypto->getName(),
                'ref' => $user->getReference()
            ];

            //Admin
            $this->mailService->sendMail(
                'CRYPTIZY - Ordre de transfert : '.$user->getEmail().' - Montant transféré: '.$montant,
                ['contact@cryptizy.com' => 'CRYPTIZY'],
                ["jonathan.kablan@gmail.com", "khouiel.salah@gmail.com"],
                $this->templating->render('email/admin/adminTransfertAlert.html.twig', $contentData)
            );

            //User
            $this->mailService->sendMail(
                'CRYPTIZY - Transfert en cours',
                ['contact@cryptizy.com' => 'CRYPTIZY'],
                [$user->getEmail()],
                $this->templating->render('email/user/transfertUser.html.twig', $contentData)
            );

            $this->messageService->addSuccess('Transfert de vos cryptos en cours');
        } else {
            $this->messageService->addError('Transfert echoué, crypto insuffisant !');
        }
    }

    /**
     * @param Utilisateur $user
     * @param array $data
     */
    public function updatePassword(Utilisateur $user, array $data): void
    {
        $password_old = trim($data['password_old']);
        $password = trim($data['password_new']);
        $password_confirmation = trim($data['password_confirmation']);

        if (password_verify($password_old, $user->getPassword())) {
            if ($password == $password_confirmation) {
                $user->setPassword($this->passwordService->encode($user, $password));
                $this->em->persist($user);
                $this->em->flush();

                $this->messageService->addSuccess('Mot de passe mis à jour');
            } else {
                $this->messageService->addError('les deux mots de passe doivent être identiques');
            }
        } else {
            $this->messageService->addError('Ce mot de passe est incorrect !');
        }
    }

    /**
     * @param Utilisateur $user
     * @param string $emailFriend
     */
    public function addFriend(Utilisateur $user, string $emailFriend): void
    {
        $userFriendly = $this->em->getRepository(Utilisateur::class)->findOneBy(['email' => $emailFriend]);

        //Verifie que l'utilisateur existe en base
        if ($userFriendly instanceof Utilisateur) {
            $userExist = $this->friendsRepository->checkUserIsAlreadyMyFriend($user, $userFriendly);

            //Verifie que l'utilisateur n'est pas déjà amis avec celui a ajouter
            if (!$userExist) {
                $friend = new Friends();
                $friend->setUtilisateur($user);
                $friend->setUtilisateurFriend($userFriendly);
                $friend->setDisabled(0);
                $friend->setActive(0);
                $friend->setUpdated(new \DateTime());
                $friend->setCreated(new \DateTime());
                $this->em->persist($friend);
                $this->em->flush();

                $this->messageService->addSuccess('Amis ajouté');
            } else {
                $this->messageService->addError('Amis déjà ajouté');
            }
        } else {
            $this->messageService->addError('Amis ajouté inconnu !');
        }
    }

    /**
     * @param Utilisateur $user
     * @param array $data
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function newOperation(Utilisateur $user, array $data): void
    {
        $crypto = trim($data['crypto']);
        $soldeCurrent = $user->getSolde()->getSolde();

        $CryptoAdd = $this->cryptoRepository->find($data['target']);

        if (!empty($crypto) && is_numeric($crypto)) {
            if ($soldeCurrent >= $crypto) {

                //debiter le solde courent
                $newSolde = $soldeCurrent - $crypto;

                //reference generer
                $ref = strtoupper(uniqid());
                $operation = new Operation();
                $operation->setCvg(1);
                $operation->setState(0);
                $operation->setUtilisateur($user);
                $operation->setPourcentageFrais(0);
                $operation->setQuantite(0);
                $operation->setAmount(0);
                $operation->setFrais(0);
                $operation->setCost($crypto);
                $operation->setReference($ref);
                $operation->setCrypto($CryptoAdd);
                $operation->setCreated(new \DateTime());
                $this->em->persist($operation);
                $this->em->flush();

                $user->getSolde()->setSolde($newSolde);
                $this->em->persist($user);
                $this->em->flush();

                // Envoie du mail de confirmation
                $contentData = [
                    'prenom' => $user->getPrenom(),
                    'crypto_name' => $CryptoAdd->getName()
                ];

                $this->mailService->sendMail(
                    'CRYPTIZY - Ordre d\'achat',
                    ['contact@cryptizy.com' => 'CRYPTIZY'],
                    [$user->getEmail()],
                    $this->templating->render('email/user/buyUser.html.twig', $contentData)
                );

                $this->mailService->sendMail(
                    'CRYPTIZY - Ordre d\'achat',
                    ['contact@cryptizy.com' => 'CRYPTIZY'],
                    ["jonathan.kablan@gmail.com", "khouiel.salah@gmail.com"],
                    $this->templating->render('email/admin/adminBuyOrder.html.twig', $contentData)
                );

                $this->messageService->addSuccess('L\'ordre d\'achat a bien été pris en compte.');
            } else {
                $this->messageService->addError('Votre Solde est insuffisant pour soumettre des ordres.');
            }
        }
    }

    /**
     * @param Utilisateur $user
     * @param array $data
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function addWallet(Utilisateur $user, array $data): void
    {
        $description = trim($data['description']);
        $adresse = trim($data['adresse']);
        $crypto_id = trim($data['crypto_id']);

        $Crypto = $this->cryptoRepository;
        $CryptoChoose = $Crypto->find($crypto_id);

        $wallet = new Adressewallet();
        $wallet->setAdresse($adresse);
        $wallet->setCrypto($CryptoChoose);
        $wallet->setDescription($description);
        $wallet->setUtilisateur($user);
        $wallet->setState(1);
        $wallet->setUpdated(new \DateTime());
        $wallet->setCreated(new \DateTime());
        $this->em->persist($wallet);
        $this->em->flush();

        //Admin
        $contentData = [
            'nom' => $user->getNom(),
            'prenom' => $user->getPrenom(),
            'reference' => $user->getReference(),
            'crypto_name' => $CryptoChoose->getName()
        ];

        $this->mailService->sendMail(
            'CRYPTIZY - Nouvelle adresse Wallet enregistrée : '.$user->getEmail().'!',
            ['contact@cryptizy.com' => 'CRYPTIZY'],
            ["jonathan.kablan@gmail.com", "khouiel.salah@gmail.com"],
            $this->templating->render('email/admin/adminWalletAdd.html.twig', $contentData)
        );

        $this->messageService->addSuccess('Wallet ajouté avec succès.');
    }

    /**
     * @param Utilisateur $user
     * @param Crypto $CryptoAdd
     * @param $newSoldeCrypto
     * @param $crypto
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function debitCryptoBtcSolde(Utilisateur $user, Crypto $CryptoAdd, $newSoldeCrypto, $crypto): void
    {
        $user->getSoldecrypto()->setBtcSolde($newSoldeCrypto);
        $this->em->persist($user);
        $this->em->flush();

        // Envoie du mail de confirmation
        $contentData = [
            'nom' => $user->getNom(),
            'prenom' => $user->getPrenom(),
            'crypto_name' => $CryptoAdd->getName(),
            'quantite' => $crypto
        ];

        $this->mailService->sendMail(
            'CRYPTIZY - Nouvel ordre de vente',
            ['contact@cryptizy.com' => 'CRYPTIZY'],
            ["jonathan.kablan@gmail.com", "khouiel.salah@gmail.com"],
            $this->templating->render('email/admin/adminSellOrder.html.twig', $contentData)
        );

        $this->messageService->addSuccess('L\'ordre de vente a bien été pris en compte.');
    }

    /**
     * @param Utilisateur $user
     * @param Crypto $CryptoAdd
     * @param $newSoldeCrypto
     * @param $crypto
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function debitCryptoBchSolde(Utilisateur $user, Crypto $CryptoAdd, $newSoldeCrypto, $crypto): void
    {
        $user->getSoldecrypto()->setBchSolde($newSoldeCrypto);
        $this->em->persist($user);
        $this->em->flush();

        // Envoie du mail de confirmation
        $contentData = [
            'nom' => $user->getNom(),
            'prenom' => $user->getPrenom(),
            'crypto_name' => $CryptoAdd->getName(),
            'quantite' => $crypto
        ];

        $this->mailService->sendMail(
            'CRYPTIZY - Nouvel ordre de vente',
            ['contact@cryptizy.com' => 'CRYPTIZY'],
            ["jonathan.kablan@gmail.com", "khouiel.salah@gmail.com"],
            $this->templating->render('email/admin/adminSellOrder.html.twig', $contentData)
        );

        $this->messageService->addSuccess('L\'ordre de vente a bien été pris en compte.');
    }

    /**
     * @param Utilisateur $user
     * @param Crypto $CryptoAdd
     * @param $newSoldeCrypto
     * @param $crypto
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function debitCryptoEthSolde(Utilisateur $user, Crypto $CryptoAdd, $newSoldeCrypto, $crypto): void
    {
        $user->getSoldecrypto()->setEthSolde($newSoldeCrypto);
        $this->em->persist($user);
        $this->em->flush();

        // Envoie du mail de confirmation
        $contentData = [
            'nom' => $user->getNom(),
            'prenom' => $user->getPrenom(),
            'crypto_name' => $CryptoAdd->getName(),
            'quantite' => $crypto
        ];

        $this->mailService->sendMail(
            'CRYPTIZY - Nouvel ordre de vente',
            ['contact@cryptizy.com' => 'CRYPTIZY'],
            ["jonathan.kablan@gmail.com", "khouiel.salah@gmail.com"],
            $this->templating->render('email/admin/adminSellOrder.html.twig', $contentData)
        );

        $this->messageService->addSuccess('L\'ordre de vente a bien été pris en compte.');
    }

    /**
     * @param Utilisateur $user
     * @param Crypto $CryptoAdd
     * @param $newSoldeCrypto
     * @param $crypto
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function debitCryptoEtcSolde(Utilisateur $user, Crypto $CryptoAdd, $newSoldeCrypto, $crypto): void
    {
        $user->getSoldecrypto()->setEtcSolde($newSoldeCrypto);
        $this->em->persist($user);
        $this->em->flush();

        // Envoie du mail de confirmation
        $contentData = [
            'nom' => $user->getNom(),
            'prenom' => $user->getPrenom(),
            'crypto_name' => $CryptoAdd->getName(),
            'quantite' => $crypto
        ];

        $this->mailService->sendMail(
            'CRYPTIZY - Nouvel ordre de vente',
            ['contact@cryptizy.com' => 'CRYPTIZY'],
            ["jonathan.kablan@gmail.com", "khouiel.salah@gmail.com"],
            $this->templating->render('email/admin/adminSellOrder.html.twig', $contentData)
        );

        $this->messageService->addSuccess('L\'ordre de vente a bien été pris en compte.');
    }

    /**
     * @param Utilisateur $user
     * @param Crypto $CryptoAdd
     * @param $newSoldeCrypto
     * @param $crypto
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function debitCryptoLtcSolde(Utilisateur $user, Crypto $CryptoAdd, $newSoldeCrypto, $crypto): void
    {
        $user->getSoldecrypto()->setLtcSolde($newSoldeCrypto);
        $this->em->persist($user);
        $this->em->flush();

        // Envoie du mail de confirmation
        $contentData = [
            'nom' => $user->getNom(),
            'prenom' => $user->getPrenom(),
            'crypto_name' => $CryptoAdd->getName(),
            'quantite' => $crypto
        ];

        $this->mailService->sendMail(
            'CRYPTIZY - Nouvel ordre de vente',
            ['contact@cryptizy.com' => 'CRYPTIZY'],
            ["jonathan.kablan@gmail.com", "khouiel.salah@gmail.com"],
            $this->templating->render('email/admin/adminSellOrder.html.twig', $contentData)
        );

        $this->messageService->addSuccess('L\'ordre de vente a bien été pris en compte.');
    }

    /**
     * @param Utilisateur $user
     * @param Crypto $CryptoAdd
     * @param $newSoldeCrypto
     * @param $crypto
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function debitCryptoZecSolde(Utilisateur $user, Crypto $CryptoAdd, $newSoldeCrypto, $crypto): void
    {
        $user->getSoldecrypto()->setZecSolde($newSoldeCrypto);
        $this->em->persist($user);
        $this->em->flush();

        // Envoie du mail de confirmation
        $contentData = [
            'nom' => $user->getNom(),
            'prenom' => $user->getPrenom(),
            'crypto_name' => $CryptoAdd->getName(),
            'quantite' => $crypto
        ];

        $this->mailService->sendMail(
            'CRYPTIZY - Nouvel ordre de vente',
            ['contact@cryptizy.com' => 'CRYPTIZY'],
            ["jonathan.kablan@gmail.com", "khouiel.salah@gmail.com"],
            $this->templating->render('email/admin/adminSellOrder.html.twig', $contentData)
        );

        $this->messageService->addSuccess('L\'ordre de vente a bien été pris en compte.');
    }

    /**
     * @param Utilisateur $user
     * @param Crypto $CryptoAdd
     * @param $newSoldeCrypto
     * @param $crypto
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function debitCryptoXrpSolde(Utilisateur $user, Crypto $CryptoAdd, $newSoldeCrypto, $crypto): void
    {
        $user->getSoldecrypto()->setXrpSolde($newSoldeCrypto);
        $this->em->persist($user);
        $this->em->flush();

        // Envoie du mail de confirmation
        $contentData = [
            'nom' => $user->getNom(),
            'prenom' => $user->getPrenom(),
            'crypto_name' => $CryptoAdd->getName(),
            'quantite' => $crypto
        ];

        $this->mailService->sendMail(
            'CRYPTIZY - Nouvel ordre de vente',
            ['contact@cryptizy.com' => 'CRYPTIZY'],
            ["jonathan.kablan@gmail.com", "khouiel.salah@gmail.com"],
            $this->templating->render('email/admin/adminSellOrder.html.twig', $contentData)
        );

        $this->messageService->addSuccess('L\'ordre de vente a bien été pris en compte.');
    }

    /**
     * @param Utilisateur $user
     * @param Crypto $CryptoAdd
     * @param $newSoldeCrypto
     * @param $crypto
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function debitCryptoEosSolde(Utilisateur $user, Crypto $CryptoAdd, $newSoldeCrypto, $crypto): void
    {
        $user->getSoldecrypto()->setEosSolde($newSoldeCrypto);
        $this->em->persist($user);
        $this->em->flush();

        // Envoie du mail de confirmation
        $contentData = [
            'nom' => $user->getNom(),
            'prenom' => $user->getPrenom(),
            'crypto_name' => $CryptoAdd->getName(),
            'quantite' => $crypto
        ];

        $this->mailService->sendMail(
            'CRYPTIZY - Nouvel ordre de vente',
            ['contact@cryptizy.com' => 'CRYPTIZY'],
            ["jonathan.kablan@gmail.com", "khouiel.salah@gmail.com"],
            $this->templating->render('email/admin/adminSellOrder.html.twig', $contentData)
        );

        $this->messageService->addSuccess('L\'ordre de vente a bien été pris en compte.');
    }

    /**
     * @param Utilisateur $user
     * @param $solde
     * @param $montant
     * @param $marge
     * @return string
     */
    public function virement(Utilisateur $user, $solde, $montant, $marge)
    {
        $depot = new Depot();
        $depot->setUtilisateur($user);
        $depot->setSolde($solde);
        $depot->setMontant($montant);
        $depot->setMarge($marge);
        $depot->setSource('lydia');
        $depot->setDisable(0);
        $depot->setState(0);
        $depot->setUpdated(new \DateTime());
        $depot->setCreated(new \DateTime());
        $this->em->persist($depot);
        $this->em->flush();

        $url = "https://reparizy.com/api/interplatform";
        $valeur = [
            'montant' => $montant,
            'telephone' => $user->getTelephone(),
        ];
        $urlLydia = $this->queryCurl->postQuery($url, $valeur);

        if (isset($urlLydia)) {
            return stripslashes(substr(ltrim($urlLydia, '"'), 0, -1));
        }

        return "https://cryptizy.com";
    }

    /**
     * @param Utilisateur $user
     * @param array $data
     */
    public function verif(Utilisateur $user, array $data): void
    {
        $user->setAdresse(trim($data['adresse']));
        $user->setCodepostale(trim($data['codepostale']));
        $user->setVille(trim($data['ville']));
        $this->em->persist($user);
        $this->em->flush();

        $this->messageService->addSuccess('Compte mis à jour');
    }

    /**
     * @param Utilisateur $user
     * @param $solde
     */
    public function setBtcSolde(Utilisateur $user, $solde): void
    {
        $user->getSoldecrypto()->setBtcSolde($solde);
        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * @param Utilisateur $FriendUser
     * @param $solde
     */
    public function creditBtcSolde(Utilisateur $FriendUser, $solde): void
    {
        $FriendUser->getSoldecrypto()->setBtcSolde($solde);
        $this->em->persist($FriendUser);
        $this->em->flush();

        $this->messageService->addSuccess('Transfert en cours');
    }

    /**
     * @param Utilisateur $user
     * @param $solde
     */
    public function setBchSolde(Utilisateur $user, $solde): void
    {
        $user->getSoldecrypto()->setBchSolde($solde);
        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * @param Utilisateur $FriendUser
     * @param $solde
     */
    public function creditBchSolde(Utilisateur $FriendUser, $solde): void
    {
        $FriendUser->getSoldecrypto()->setBchSolde($solde);
        $this->em->persist($FriendUser);
        $this->em->flush();

        $this->messageService->addSuccess('Transfert en cours');
    }

    /**
     * @param Utilisateur $user
     * @param $solde
     */
    public function setEthSolde(Utilisateur $user, $solde): void
    {
        $user->getSoldecrypto()->setEthSolde($solde);
        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * @param Utilisateur $FriendUser
     * @param $solde
     */
    public function creditEthSolde(Utilisateur $FriendUser, $solde): void
    {
        $FriendUser->getSoldecrypto()->setEthSolde($solde);
        $this->em->persist($FriendUser);
        $this->em->flush();

        $this->messageService->addSuccess('Transfert en cours');
    }

    /**
     * @param Utilisateur $user
     * @param $solde
     */
    public function setEtcSolde(Utilisateur $user, $solde): void
    {
        $user->getSoldecrypto()->setEtcSolde($solde);
        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * @param Utilisateur $FriendUser
     * @param $solde
     */
    public function creditEtcSolde(Utilisateur $FriendUser, $solde): void
    {
        $FriendUser->getSoldecrypto()->setEtcSolde($solde);
        $this->em->persist($FriendUser);
        $this->em->flush();

        $this->messageService->addSuccess('Transfert en cours');
    }

    /**
     * @param Utilisateur $user
     * @param $solde
     */
    public function setLtcSolde(Utilisateur $user, $solde): void
    {
        $user->getSoldecrypto()->setLtcSolde($solde);
        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * @param Utilisateur $FriendUser
     * @param $solde
     */
    public function creditLtcSolde(Utilisateur $FriendUser, $solde): void
    {
        $FriendUser->getSoldecrypto()->setLtcSolde($solde);
        $this->em->persist($FriendUser);
        $this->em->flush();

        $this->messageService->addSuccess('Transfert en cours');
    }

    /**
     * @param Utilisateur $user
     * @param $solde
     */
    public function setZecSolde(Utilisateur $user, $solde): void
    {
        $user->getSoldecrypto()->setZecSolde($solde);
        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * @param Utilisateur $FriendUser
     * @param $solde
     */
    public function creditZecSolde(Utilisateur $FriendUser, $solde): void
    {
        $FriendUser->getSoldecrypto()->setZecSolde($solde);
        $this->em->persist($FriendUser);
        $this->em->flush();

        $this->messageService->addSuccess('Transfert en cours');
    }

    /**
     * @param Utilisateur $user
     * @param $solde
     */
    public function setXrpSolde(Utilisateur $user, $solde): void
    {
        $user->getSoldecrypto()->setXrpSolde($solde);
        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * @param Utilisateur $FriendUser
     * @param $solde
     */
    public function creditXrpSolde(Utilisateur $FriendUser, $solde): void
    {
        $FriendUser->getSoldecrypto()->setXrpSolde($solde);
        $this->em->persist($FriendUser);
        $this->em->flush();

        $this->messageService->addSuccess('Transfert en cours');
    }

    /**
     * @param Utilisateur $user
     * @param $solde
     */
    public function setEosSolde(Utilisateur $user, $solde): void
    {
        $user->getSoldecrypto()->setEosSolde($solde);
        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * @param Utilisateur $FriendUser
     * @param $solde
     */
    public function creditEosSolde(Utilisateur $FriendUser, $solde): void
    {
        $FriendUser->getSoldecrypto()->setEosSolde($solde);
        $this->em->persist($FriendUser);
        $this->em->flush();

        $this->messageService->addSuccess('Transfert en cours');
    }

    /**
     * @param Utilisateur $user
     * @param array $data
     */
    public function addIban(Utilisateur $user, array $data): void
    {
        $iban = trim($data['iban']);
        $bic = trim($data['bic']);
        $user->setIban($iban);
        $user->setBic($bic);
        $this->em->persist($user);
        $this->em->flush();

        $this->messageService->addSuccess('Rib ajouté');
    }

    /**
     * @param array $data
     */
    public function activeFriend(array $data): void
    {
        $valideFriend = $this->em->getRepository(Friends::class)->find(trim($data['id']));
        $valideFriend->setActive(1);
        $this->em->persist($valideFriend);
        $this->em->flush();

        $this->messageService->addSuccess('Amis valide');
    }

    /**
     * @param Utilisateur $user
     * @param string $name
     * @param string $pathSource
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function loadIdentite(Utilisateur $user, string $name, string $pathSource): void
    {
        $user->setLoadIdentite($name);
        $this->em->flush();

        // Envoie du mail de confirmation
        $contentData = [
            'prenom' => $user->getPrenom(),
            'nom' => $user->getNom(),
            'ref' => $user->getReference(),
            'type' => "Piece d'identité",
            'source' => $pathSource.$user->getLoadIdentite()
        ];

        $this->mailService->sendMail(
            'CRYPTIZY - DOCUMENT : Pièce d\'identité télécharger - '.$user->getPrenom().' '.$user->getNom(),
            ['contact@cryptizy.com' => 'CRYPTIZY'],
            ["jonathan.kablan@gmail.com", "khouiel.salah@gmail.com"],
            $this->templating->render('email/admin/adminFileVerif.html.twig', $contentData)
        );

        $this->messageService->addSuccess('Pièce d\'identité envoyée');
    }

    /**
     * @param Utilisateur $user
     * @param string $name
     * @param string $pathSource
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function loadJustificatifDomicile(Utilisateur $user, string $name, string $pathSource): void
    {
        $user->setLoadJustificatifDomicile($name);
        $this->em->flush();

        // Envoie du mail de confirmation
        $contentData = [
            'prenom' => $user->getPrenom(),
            'nom' => $user->getNom(),
            'ref' => $user->getReference(),
            'type' => "Justificatif Domicile",
            'source' => $pathSource.$user->getLoadJustificatifDomicile()
        ];

        $this->mailService->sendMail(
            'CRYPTIZY - DOCUMENT : Justificatif de domicile télécharger - '.$user->getPrenom().' '.$user->getNom(),
            ['contact@cryptizy.com' => 'CRYPTIZY'],
            ["jonathan.kablan@gmail.com", "khouiel.salah@gmail.com"],
            $this->templating->render('email/admin/adminFileVerif.html.twig', $contentData)
        );

        $this->messageService->addSuccess('Justificatif de domicile envoyé');
    }

    /**
     * @param Utilisateur $user
     * @param string $name
     * @param string $pathSource
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function loadJustificatifSelfie(Utilisateur $user, string $name, string $pathSource): void
    {
        $user->setJustificatifSelfie($name);
        $this->em->flush();

        // Envoie du mail de confirmation
        $contentData = [
            'prenom' => $user->getPrenom(),
            'nom' => $user->getNom(),
            'ref' => $user->getReference(),
            'type' => "Justificatif Selfie",
            'source' => $pathSource.$user->getJustificatifSelfie()
        ];

        $this->mailService->sendMail(
            'CRYPTIZY - DOCUMENT : Justificatif par selfie télécharger - '.$user->getPrenom().' '.$user->getNom(),
            ['contact@cryptizy.com' => 'CRYPTIZY'],
            ["jonathan.kablan@gmail.com", "khouiel.salah@gmail.com"],
            $this->templating->render('email/admin/adminFileVerif.html.twig', $contentData)
        );

        $this->messageService->addSuccess('Justificatif du selfie envoyé');
    }

    /**
     * @param array $AllFilleules
     * @param Utilisateur $user
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function parainnage(array $AllFilleules, Utilisateur $user)
    {
        if ($AllFilleules) {
            foreach ($AllFilleules as $filleul) {
                $checkSoldeOperationFilleul = $this->operationRepository->getAllOperationSuccessByUser($filleul->getUtilisateurFilleul());
                if ($checkSoldeOperationFilleul) {
                    if ($checkSoldeOperationFilleul[0]['total'] >= 100) {
                        $parrain_ = $this->parainageRepository->getCheckParainUserFilleul($user, $filleul->getUtilisateurFilleul());
                        $parrain = $parrain_[0];

                        $parrain->setEtat(1);
                        $this->em->persist($parrain);
                        $this->em->flush();

                        //Calcul pour deduire le nouveau solde parrain
                        $newSoldeCurrent_parrain = $user->getSolde()->getSolde() + $parrain->getSolde();
                        $newSoldeOld_parrain = $user->getSolde()->getSoldeOld() + $parrain->getSolde();
                        $user->getSolde()->setSolde($newSoldeCurrent_parrain);
                        $user->getSolde()->setSoldeOld($newSoldeOld_parrain);
                        $user->getSolde()->setUpdated(new \DateTime());
                        $this->em->persist($user);
                        $this->em->flush();

                        //Calcul pour deduire le nouveau solde filleul
                        $newSoldeCurrent_filleul = $filleul->getUtilisateurFilleul()->getSolde()->getSolde() + $parrain->getSolde();
                        $newSoldeOld_filleul = $filleul->getUtilisateurFilleul()->getSolde()->getSoldeOld() + $parrain->getSolde();
                        $filleul->getUtilisateurFilleul()->getSolde()->setSolde($newSoldeCurrent_filleul);
                        $filleul->getUtilisateurFilleul()->getSolde()->setSoldeOld($newSoldeOld_filleul);
                        $filleul->getUtilisateurFilleul()->getSolde()->setUpdated(new \DateTime());
                        $this->em->persist($filleul->getUtilisateurFilleul());
                        $this->em->flush();

                        // Envoie du mail de confirmation
                        $this->mailService->sendMail(
                            'CRYPTIZY - Récompense de parrainage',
                            ['contact@cryptizy.com' => 'CRYPTIZY'],
                            [$user->getEmail(), $filleul->getUtilisateurFilleul()->getEmail()],
                            $this->templating->render('email/user/recompenseParrainage.html.twig', [])
                        );
                    }
                }
            }
        }
    }
}
