<?php

namespace App\Manager\Api;

use App\Entity\Adressewallet;
use App\Entity\Crypto;
use App\Entity\Operation;
use App\Entity\Retrait;
use App\Entity\Transfertcrypto;
use App\Entity\Utilisateur;
use App\Entity\Vendre;
use App\Repository\AdressewalletRepository;
use App\Repository\CryptoRepository;
use App\Repository\OperationRepository;
use App\Repository\ParainageRepository;
use App\Repository\TransfertcryptoRepository;
use App\Repository\UtilisateurRepository;
use App\Services\MailService;
use App\Services\MessageService;
use App\Services\PasswordService;
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
     * @var AdressewalletRepository
     */
    protected $adressewalletRepository;

    /**
     * @var TransfertcryptoRepository
     */
    protected $transfertcryptoRepository;

    /**
     * @param EntityManagerInterface $em
     * @param MailService $mailService
     * @param \Twig_Environment $templating
     * @param MessageService $messageService
     * @param UtilisateurRepository $utilisateurRepository
     * @param OperationRepository $operationRepository
     * @param ParainageRepository $parainageRepository
     * @param CryptoRepository $cryptoRepository
     * @param AdressewalletRepository $adressewalletRepository
     * @param TransfertcryptoRepository $transfertcryptoRepository
     */
    public function __construct(
        EntityManagerInterface $em,
        MailService $mailService,
        \Twig_Environment $templating,
        MessageService $messageService,
        UtilisateurRepository $utilisateurRepository,
        OperationRepository $operationRepository,
        ParainageRepository $parainageRepository,
        CryptoRepository $cryptoRepository,
        AdressewalletRepository $adressewalletRepository,
        TransfertcryptoRepository $transfertcryptoRepository
    ) {
        $this->em = $em;
        $this->mailService = $mailService;
        $this->templating = $templating;
        $this->messageService = $messageService;
        $this->utilisateurRepository = $utilisateurRepository;
        $this->operationRepository = $operationRepository;
        $this->parainageRepository = $parainageRepository;
        $this->cryptoRepository = $cryptoRepository;
        $this->adressewalletRepository = $adressewalletRepository;
        $this->transfertcryptoRepository = $transfertcryptoRepository;
    }

    /**
     * @param Utilisateur $utilisateur
     * @param array $data
     * @return array
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function reservationCrypto(Utilisateur $utilisateur, array $data)
    {
        //verification du profil valide
        if ($utilisateur->getValidated() == 1) {

            //Get all data send by post method
            $crypto = trim($data['cryptoAmount']);
            $soldeCurrent = $utilisateur->getSolde()->getSolde();

            $id = $data['targetCrypto'];
            $CryptoAdd = $this->cryptoRepository->find($id);

            if (!empty($crypto) && is_numeric($crypto)) {
                if ($soldeCurrent >= $crypto && $crypto >= 50) {

                    //debiter le solde courent
                    $newSolde = $soldeCurrent - $crypto;

                    //reference generer
                    $ref = strtoupper(uniqid());
                    $operation = new Operation();
                    $operation->setCvg(1);
                    $operation->setState(0);
                    $operation->setUtilisateur($utilisateur);
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

                    $utilisateur->getSolde()->setSolde($newSolde);
                    $this->em->persist($utilisateur);
                    $this->em->flush();

                    // Envoie du mail de confirmation
                    $contentData = [
                        'prenom' => $utilisateur->getPrenom(),
                        'crypto_name' => $CryptoAdd->getName()
                    ];

                    $this->mailService->sendMail(
                        'CRYPTIZY - Ordre d\'achat',
                        ['contact@cryptizy.com' => 'CRYPTIZY'],
                        [$utilisateur->getEmail()],
                        $this->templating->render('email/user/buyUser.html.twig', $contentData)
                    );

                    $this->mailService->sendMail(
                        'CRYPTIZY - Ordre d\'achat',
                        ['contact@cryptizy.com' => 'CRYPTIZY'],
                        ["jonathan.kablan@gmail.com", "khouiel.salah@gmail.com"],
                        $this->templating->render('email/admin/confirmBuy.html.twig', $contentData)
                    );

                    return  ['success' => 'L\'ordre d\'achat a bien été pris en compte.'];
                } else {
                    return  ['error' => 'Votre Solde est insuffisant pour soumettre des ordres il doit etre en moyenne superieur a 50 €.'];
                }
            }

            return  ['error' => 'Votre Solde est peut etre insuffisant.'];
        } else {
            return ['error' => 'Votre profil doit être vérifié pour soumettre des ordres. Allez dans Vérification pour faire vérifier votre compte.'];
        }
    }

    /**
     * @param Utilisateur $utilisateur
     * @param array $data
     * @return array
     */
    public function addRib(Utilisateur $utilisateur, array $data)
    {
        if (!$utilisateur instanceof Utilisateur) {
            return ['error' => 'User not found'];
        }

        $iban = trim($data['iban']);
        $bic = trim($data['bic']);
        $utilisateur->setIban($iban);
        $utilisateur->setBic($bic);
        $this->em->persist($utilisateur);
        $this->em->flush();

        return ['success' => 'Rib ajouté'];
    }

    /**
     * @param Utilisateur $utilisateur
     * @param array $data
     * @return array
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function addWallet(Utilisateur $utilisateur, array $data)
    {
        if (!$utilisateur instanceof Utilisateur) {
            return ['error' => 'User not found'];
        }

        if (isset($data['description']) && $data['description'] !== null) {
            $description = trim($data['description']);
        } else {
            $description = " ... ";
        }

        if (isset($data['adresse']) && isset($data['crypto_id']) )  {
            $adresse = trim($data['adresse']);
            $crypto_id = trim($data['crypto_id']);

            if (is_numeric($crypto_id) && $crypto_id >= 1) {
                $CryptoChoose = $this->cryptoRepository->find($crypto_id);

                $wallet = new Adressewallet();
                $wallet->setAdresse($adresse);
                $wallet->setCrypto($CryptoChoose);
                $wallet->setDescription($description);
                $wallet->setUtilisateur($utilisateur);
                $wallet->setState(1);
                $wallet->setUpdated(new \DateTime());
                $wallet->setCreated(new \DateTime());
                $this->em->persist($wallet);
                $this->em->flush();

                // Envoie du mail de confirmation
                $contentData = [
                    'nom' => $utilisateur->getNom(),
                    'prenom' => $utilisateur->getPrenom(),
                    'reference' => $utilisateur->getReference(),
                    'crypto_name' => $CryptoChoose->getName()
                ];

                $this->mailService->sendMail(
                    'CRYPTIZY - Nouvelle adresse Wallet enregistrée : '.$utilisateur->getEmail().'!',
                    ['contact@cryptizy.com' => 'CRYPTIZY'],
                    ["jonathan.kablan@gmail.com", "khouiel.salah@gmail.com"],
                    $this->templating->render('email/admin/adminWalletAdd.html.twig', $contentData)
                );

                return ['success' => 'Wallet ajouté'];
            }

            return ['error' => 'Indice Crypto is not numeric .' ];
        }

        return ['error' => 'Adresse or Crypto is not found .' ];
    }

    /**
     * @param Utilisateur $utilisateur
     * @param array $data
     * @return array
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function retrait(Utilisateur $utilisateur, array $data)
    {
        if (!$utilisateur instanceof Utilisateur) {
            $data =  ['error' => 'User not found'];
        }

        $solde = trim($data['solde']);
        $soldeCurrent = $utilisateur->getSolde()->getSolde();

        if ($soldeCurrent >= $solde) {
            if ($utilisateur->getIban() !== null && $utilisateur->getBic() !== null) {
                $transfert = new Retrait();
                $transfert->setState(0);
                $transfert->setMarge(5);
                $transfert->setSolde($solde);
                $transfert->setMontant($solde-5);
                $transfert->setUpdated(new \DateTime());
                $transfert->setCreated(new \DateTime());
                $transfert->setUtilisateur($utilisateur);
                $this->em->persist($transfert);
                $this->em->flush();

                //debiter le solde courent
                $newSolde = $soldeCurrent - $solde;

                $utilisateur->getSolde()->setSolde($newSolde);
                $this->em->persist($utilisateur);
                $this->em->flush();

                // Envoie du mail de confirmation
                $contentData = [
                    'nom' => $utilisateur->getNom(),
                    'prenom' => $utilisateur->getPrenom()
                ];
                //Admin
                $this->mailService->sendMail(
                    'CRYPTIZY - Demande de Retrait : '.$utilisateur->getEmail().'!',
                    ['contact@cryptizy.com' => 'CRYPTIZY'],
                    ["jonathan.kablan@gmail.com", "khouiel.salah@gmail.com"],
                    $this->templating->render('email/admin/adminRetrait.html.twig', $contentData)
                );

                //User
                $this->mailService->sendMail(
                    'CRYPTIZY - Traitement de Retrait.',
                    ['contact@cryptizy.com' => 'CRYPTIZY'],
                    [$utilisateur->getEmail()],
                    $this->templating->render('email/user/retraitUser.html.twig', $contentData)
                );

                $data = ['success' => 'Retrait ajouté'];
            } else {
                $data = ['error' => 'Pensez à ajouter votre RIB et BIC avant merci .'];
            }
        } else {
            $data = ['error' => 'Votre solde est insuffisant pour cette opération.'];
        }

        return $data;
    }

    /**
     * @param Utilisateur $utilisateur
     * @param array $data
     * @return array
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sellCrypto(Utilisateur $utilisateur, array $data)
    {
        if (!$utilisateur instanceof Utilisateur) {
            return ['error' => 'User not found'];
        }
        $check = 0;

        if (!isset($data['target'])) {
            $check = 1;
        }
        if (!is_numeric($data['target'])) {
            $check = 1;
        }

        if ($check !== 1) {
            $crypto = trim($data['crypto']);
            $soldeCurrent = $utilisateur->getSolde()->getSolde();
            $cryptoCurrent = 0;

            $CryptoAdd = $this->cryptoRepository->find($data['target']);

            if ($CryptoAdd instanceof Crypto) {
                $SoldeIndexes = $utilisateur->getSoldecrypto();
                $vendre = new Vendre();
                $stateVente = 1;

                switch ($data['target']) {
                    case 1:
                        $cryptoCurrent = $SoldeIndexes->getBtcSolde();
                        if ($crypto <= $cryptoCurrent) {
                            $vendre->setUtilisateur($utilisateur);
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

                            $newSoldeCrypto = $cryptoCurrent - $crypto;
                            $utilisateur->getSoldecrypto()->setBtcSolde($newSoldeCrypto);
                            $this->em->persist($utilisateur);
                            $this->em->flush();

                            // Envoie du mail de confirmation
                            $contentData = [
                                'nom' => $utilisateur->getNom(),
                                'prenom' => $utilisateur->getPrenom(),
                                'crypto_name' => $CryptoAdd->getName(),
                                'quantite' => $crypto
                            ];

                            $this->mailService->sendMail(
                                'CRYPTIZY - Nouvel ordre de vente.',
                                ['contact@cryptizy.com' => 'CRYPTIZY'],
                                ["jonathan.kablan@gmail.com", "khouiel.salah@gmail.com"],
                                $this->templating->render('email/admin/adminSellOrder.html.twig', $contentData)
                            );

                            return ['success' => 'L\'ordre de vente a bien été pris en compte.'];
                        } else {
                            return ['error' => 'Votre Solde est insuffisant pour soumettre l\'ordre.'];
                        }
                        break;
                    case 2:
                        $cryptoCurrent = $SoldeIndexes->getBchSolde();
                        if ($crypto <= $cryptoCurrent) {
                            $vendre->setUtilisateur($utilisateur);
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

                            $newSoldeCrypto = $cryptoCurrent - $crypto;
                            $utilisateur->getSoldecrypto()->setBchSolde($newSoldeCrypto);
                            $this->em->persist($utilisateur);
                            $this->em->flush();

                            // Envoie du mail de confirmation
                            $contentData = [
                                'nom' => $utilisateur->getNom(),
                                'prenom' => $utilisateur->getPrenom(),
                                'crypto_name' => $CryptoAdd->getName(),
                                'quantite' => $crypto
                            ];

                            $this->mailService->sendMail(
                                'CRYPTIZY - Nouvel ordre de vente.',
                                ['contact@cryptizy.com' => 'CRYPTIZY'],
                                ["jonathan.kablan@gmail.com", "khouiel.salah@gmail.com"],
                                $this->templating->render('email/admin/adminSellOrder.html.twig', $contentData)
                            );

                            return ['success' => 'L\'ordre de vente a bien été pris en compte.'];
                        } else {
                            return ['error' => 'Votre Solde est insuffisant pour soumettre l\'ordre.'];
                        }
                        break;
                    case 3:
                        $cryptoCurrent = $SoldeIndexes->getEthSolde();
                        if ($crypto <= $cryptoCurrent) {
                            $vendre->setUtilisateur($utilisateur);
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

                            $newSoldeCrypto = $cryptoCurrent - $crypto;
                            $utilisateur->getSoldecrypto()->setEthSolde($newSoldeCrypto);
                            $this->em->persist($utilisateur);
                            $this->em->flush();

                            // Envoie du mail de confirmation
                            $contentData = [
                                'nom' => $utilisateur->getNom(),
                                'prenom' => $utilisateur->getPrenom(),
                                'crypto_name' => $CryptoAdd->getName(),
                                'quantite' => $crypto
                            ];

                            $this->mailService->sendMail(
                                'CRYPTIZY - Nouvel ordre de vente.',
                                ['contact@cryptizy.com' => 'CRYPTIZY'],
                                ["jonathan.kablan@gmail.com", "khouiel.salah@gmail.com"],
                                $this->templating->render('email/admin/adminSellOrder.html.twig', $contentData)
                            );

                            return ['success' => 'L\'ordre de vente a bien été pris en compte.'];
                        } else {
                            return ['error' => 'Votre Solde est insuffisant pour soumettre l\'ordre.'];
                        }
                        break;
                    case 4:
                        $cryptoCurrent = $SoldeIndexes->getEtcSolde();
                        if ($crypto <= $cryptoCurrent) {
                            $vendre->setUtilisateur($utilisateur);
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

                            $newSoldeCrypto = $cryptoCurrent - $crypto;
                            $utilisateur->getSoldecrypto()->setEtcSolde($newSoldeCrypto);
                            $this->em->persist($utilisateur);
                            $this->em->flush();

                            // Envoie du mail de confirmation
                            $contentData = [
                                'nom' => $utilisateur->getNom(),
                                'prenom' => $utilisateur->getPrenom(),
                                'crypto_name' => $CryptoAdd->getName(),
                                'quantite' => $crypto
                            ];

                            $this->mailService->sendMail(
                                'CRYPTIZY - Nouvel ordre de vente.',
                                ['contact@cryptizy.com' => 'CRYPTIZY'],
                                ["jonathan.kablan@gmail.com", "khouiel.salah@gmail.com"],
                                $this->templating->render('email/admin/adminSellOrder.html.twig', $contentData)
                            );

                            return ['success' => 'L\'ordre de vente a bien été pris en compte.'];
                        } else {
                            return ['error' => 'Votre Solde est insuffisant pour soumettre l\'ordre.'];
                        }
                        break;
                    case 5:
                        $cryptoCurrent = $SoldeIndexes->getLtcSolde();
                        if ($crypto <= $cryptoCurrent) {
                            $vendre->setUtilisateur($utilisateur);
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

                            $newSoldeCrypto = $cryptoCurrent - $crypto;
                            $utilisateur->getSoldecrypto()->setLtcSolde($newSoldeCrypto);
                            $this->em->persist($utilisateur);
                            $this->em->flush();

                            // Envoie du mail de confirmation
                            $contentData = [
                                'nom' => $utilisateur->getNom(),
                                'prenom' => $utilisateur->getPrenom(),
                                'crypto_name' => $CryptoAdd->getName(),
                                'quantite' => $crypto
                            ];

                            $this->mailService->sendMail(
                                'CRYPTIZY - Nouvel ordre de vente.',
                                ['contact@cryptizy.com' => 'CRYPTIZY'],
                                ["jonathan.kablan@gmail.com", "khouiel.salah@gmail.com"],
                                $this->templating->render('email/admin/adminSellOrder.html.twig', $contentData)
                            );

                            return ['success' => 'L\'ordre de vente a bien été pris en compte.'];
                        } else {
                            return ['error' => 'Votre Solde est insuffisant pour soumettre l\'ordre.'];
                        }
                        break;
                    case 6:
                        $cryptoCurrent = $SoldeIndexes->getZecSolde();
                        if ($crypto <= $cryptoCurrent) {
                            $vendre->setUtilisateur($utilisateur);
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

                            $newSoldeCrypto = $cryptoCurrent - $crypto;
                            $utilisateur->getSoldecrypto()->setZecSolde($newSoldeCrypto);
                            $this->em->persist($utilisateur);
                            $this->em->flush();

                            // Envoie du mail de confirmation
                            $contentData = [
                                'nom' => $utilisateur->getNom(),
                                'prenom' => $utilisateur->getPrenom(),
                                'crypto_name' => $CryptoAdd->getName(),
                                'quantite' => $crypto
                            ];

                            $this->mailService->sendMail(
                                'CRYPTIZY - Nouvel ordre de vente.',
                                ['contact@cryptizy.com' => 'CRYPTIZY'],
                                ["jonathan.kablan@gmail.com", "khouiel.salah@gmail.com"],
                                $this->templating->render('email/admin/adminSellOrder.html.twig', $contentData)
                            );

                            return ['success' => 'L\'ordre de vente a bien été pris en compte.'];
                        } else {
                            return ['error' => 'Votre Solde est insuffisant pour soumettre l\'ordre.'];
                        }
                        break;
                    case 7:
                        $cryptoCurrent = $SoldeIndexes->getXrpSolde();
                        if ($crypto <= $cryptoCurrent) {
                            $vendre->setUtilisateur($utilisateur);
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

                            $newSoldeCrypto = $cryptoCurrent - $crypto;
                            $utilisateur->getSoldecrypto()->setXrpSolde($newSoldeCrypto);
                            $this->em->persist($utilisateur);
                            $this->em->flush();

                            // Envoie du mail de confirmation
                            $contentData = [
                                'nom' => $utilisateur->getNom(),
                                'prenom' => $utilisateur->getPrenom(),
                                'crypto_name' => $CryptoAdd->getName(),
                                'quantite' => $crypto
                            ];

                            $this->mailService->sendMail(
                                'CRYPTIZY - Nouvel ordre de vente.',
                                ['contact@cryptizy.com' => 'CRYPTIZY'],
                                ["jonathan.kablan@gmail.com", "khouiel.salah@gmail.com"],
                                $this->templating->render('email/admin/adminSellOrder.html.twig', $contentData)
                            );

                            return ['success' => 'L\'ordre de vente a bien été pris en compte.'];
                        } else {
                            return ['error' => 'Votre Solde est insuffisant pour soumettre l\'ordre.'];
                        }
                        break;
                    case 8:
                        $cryptoCurrent = $SoldeIndexes->getEosSolde();
                        if ($crypto <= $cryptoCurrent) {
                            $vendre->setUtilisateur($utilisateur);
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

                            $newSoldeCrypto = $cryptoCurrent - $crypto;
                            $utilisateur->getSoldecrypto()->setEosSolde($newSoldeCrypto);
                            $this->em->persist($utilisateur);
                            $this->em->flush();

                            // Envoie du mail de confirmation
                            $contentData = [
                                'nom' => $utilisateur->getNom(),
                                'prenom' => $utilisateur->getPrenom(),
                                'crypto_name' => $CryptoAdd->getName(),
                                'quantite' => $crypto
                            ];

                            $this->mailService->sendMail(
                                'CRYPTIZY - Nouvel ordre de vente.',
                                ['contact@cryptizy.com' => 'CRYPTIZY'],
                                ["jonathan.kablan@gmail.com", "khouiel.salah@gmail.com"],
                                $this->templating->render('email/admin/adminSellOrder.html.twig', $contentData)
                            );

                            return ['success' => 'L\'ordre de vente a bien été pris en compte.'];
                        } else {
                            return ['error' => 'Votre Solde est insuffisant pour soumettre l\'ordre.'];
                        }
                        break;
                    default:
                        return ['error' => 'Not found target'];
                        break;
                }
            }

            return ['error' => 'Error parameters'];
        } else {
            return ['error' => 'Error parameters'];
        }
    }

    /**
     * @param Utilisateur $utilisateur
     * @param array $data
     * @return array
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function transfertCryptoByWallet(Utilisateur $utilisateur, array $data)
    {
        if (!$utilisateur instanceof Utilisateur) {
            return ['error' => 'User not found'];
        }

        $SoldeCryptos = $utilisateur->getSoldecrypto();
        $AllCryptos = $this->cryptoRepository->findAll();

        $AllTransfertCryptos = $this->transfertcryptoRepository->findAll();
        $MyAdressewallet = $this->adressewalletRepository->getAdressewalletByUser($utilisateur);

        $check = 0;

        if (!isset($data['wallet']) || empty($data['wallet'])) {
            $check = 1;
        }
        if (!isset($data['montant']) || empty($data['montant'])) {
            $check = 1;
        }
        if (!isset($data['crypto_id']) || empty($data['crypto_id'])) {
            $check = 1;
        }
        if (!is_numeric($data['wallet']) || !is_numeric($data['montant'])) {
            $check = 1;
        }
        if (!is_numeric($data['crypto_id'])) {
            $check = 1;
        }

        if ($check !== 1) {
            $montant = trim($data['montant']);
            $crypto_id = trim($data['crypto_id']);
            $wallet_id = trim($data['wallet']);

            $setWallet = $this->adressewalletRepository->find($wallet_id);
            $setCryptos = $this->cryptoRepository->find($crypto_id);
            $SoldeIndexes = $utilisateur->getSoldecrypto();
            switch ($crypto_id) {
                case 1:
                    $cryptoCurrent = $SoldeIndexes->getBtcSolde();
                    if ($montant <= $cryptoCurrent) {

                        //reference generer
                        $transfertCrypto = new Transfertcrypto();
                        $transfertCrypto->setFrais(0);
                        $transfertCrypto->setAmount($montant);
                        $transfertCrypto->setState(1);
                        $transfertCrypto->setValidated(0);
                        $transfertCrypto->setUpdated(new \DateTime());
                        $transfertCrypto->setCreated(new \DateTime());
                        $transfertCrypto->setUtilisateur($utilisateur);
                        $transfertCrypto->setAdressewallet($setWallet);
                        $this->em->persist($transfertCrypto);
                        $this->em->flush();

                        //***************************************************
                        $newSoldeCrypto = $cryptoCurrent - $montant;
                        $utilisateur->getSoldecrypto()->setBtcSolde($newSoldeCrypto);
                        $this->em->persist($utilisateur);
                        $this->em->flush();

                        // Envoie du mail de confirmation
                        $contentData = [
                            'nom' => $utilisateur->getNom(),
                            'prenom' => $utilisateur->getPrenom(),
                            'nom_crypto' => $setWallet->getCrypto()->getName(),
                            'ref' => $utilisateur->getReference()
                        ];

                        //Admin
                        $this->mailService->sendMail(
                            'CRYPTIZY - Ordre de transfert : '.$utilisateur->getEmail().' !',
                            ['contact@cryptizy.com' => 'CRYPTIZY'],
                            ["jonathan.kablan@gmail.com", "khouiel.salah@gmail.com"],
                            $this->templating->render('email/admin/adminTransfertAlert.html.twig', $contentData)
                        );

                        //User
                        $this->mailService->sendMail(
                            'CRYPTIZY - Transfert en cours.',
                            ['contact@cryptizy.com' => 'CRYPTIZY'],
                            [$utilisateur->getEmail()],
                            $this->templating->render('email/user/transfertUser.html.twig', $contentData)
                        );

                        return ['success' => 'Transfert de vos cryptos en cours'];
                    } else {
                        return ['error' => 'Transfert echoué, crypto insuffisant !'];
                    }
                    break;
                case 2:
                    $cryptoCurrent = $SoldeIndexes->getBchSolde();
                    if ($montant <= $cryptoCurrent) {
                        //reference generer
                        $transfertCrypto = new Transfertcrypto();
                        $transfertCrypto->setFrais(0);
                        $transfertCrypto->setAmount($montant);
                        $transfertCrypto->setState(1);
                        $transfertCrypto->setValidated(0);
                        $transfertCrypto->setUpdated(new \DateTime());
                        $transfertCrypto->setCreated(new \DateTime());
                        $transfertCrypto->setUtilisateur($utilisateur);
                        $transfertCrypto->setAdressewallet($setWallet);
                        $this->em->persist($transfertCrypto);
                        $this->em->flush();

                        $newSoldeCrypto = $cryptoCurrent - $montant;
                        $utilisateur->getSoldecrypto()->setBchSolde($newSoldeCrypto);
                        $this->em->persist($utilisateur);
                        $this->em->flush();

                        // Envoie du mail de confirmation
                        $contentData = [
                            'nom' => $utilisateur->getNom(),
                            'prenom' => $utilisateur->getPrenom(),
                            'nom_crypto' => $setWallet->getCrypto()->getName(),
                            'ref' => $utilisateur->getReference()
                        ];

                        //Admin
                        $this->mailService->sendMail(
                            'CRYPTIZY - Ordre de transfert : '.$utilisateur->getEmail().'!',
                            ['contact@cryptizy.com' => 'CRYPTIZY'],
                            ["jonathan.kablan@gmail.com", "khouiel.salah@gmail.com"],
                            $this->templating->render('email/admin/adminTransfertAlert.html.twig', $contentData)
                        );

                        //User
                        $this->mailService->sendMail(
                            'CRYPTIZY - Transfert en cours.',
                            ['contact@cryptizy.com' => 'CRYPTIZY'],
                            [$utilisateur->getEmail()],
                            $this->templating->render('email/user/transfertUser.html.twig', $contentData)
                        );

                        return ['success' => 'Transfert de vos cryptos en cours'];
                    } else {
                        return ['error' => 'Transfert echoué, crypto insuffisant !'];
                    }
                    break;
                case 3:
                    $cryptoCurrent = $SoldeIndexes->getEthSolde();
                    if ($montant <= $cryptoCurrent) {
                        //reference generer
                        $transfertCrypto = new Transfertcrypto();
                        $transfertCrypto->setFrais(0);
                        $transfertCrypto->setAmount($montant);
                        $transfertCrypto->setState(1);
                        $transfertCrypto->setValidated(0);
                        $transfertCrypto->setUpdated(new \DateTime());
                        $transfertCrypto->setCreated(new \DateTime());
                        $transfertCrypto->setUtilisateur($utilisateur);
                        $transfertCrypto->setAdressewallet($setWallet);
                        $this->em->persist($transfertCrypto);
                        $this->em->flush();

                        $newSoldeCrypto = $cryptoCurrent - $montant;
                        $utilisateur->getSoldecrypto()->setEthSolde($newSoldeCrypto);
                        $this->em->persist($utilisateur);
                        $this->em->flush();

                        // Envoie du mail de confirmation
                        $contentData = [
                            'nom' => $utilisateur->getNom(),
                            'prenom' => $utilisateur->getPrenom(),
                            'nom_crypto' => $setWallet->getCrypto()->getName(),
                            'ref' => $utilisateur->getReference()
                        ];

                        //Admin
                        $this->mailService->sendMail(
                            'CRYPTIZY - Ordre de transfert : '.$utilisateur->getEmail().'!',
                            ['contact@cryptizy.com' => 'CRYPTIZY'],
                            ["jonathan.kablan@gmail.com", "khouiel.salah@gmail.com"],
                            $this->templating->render('email/admin/adminTransfertAlert.html.twig', $contentData)
                        );

                        //User
                        $this->mailService->sendMail(
                            'CRYPTIZY - Transfert en cours.',
                            ['contact@cryptizy.com' => 'CRYPTIZY'],
                            [$utilisateur->getEmail()],
                            $this->templating->render('email/user/transfertUser.html.twig', $contentData)
                        );

                        return ['success' => 'Transfert de vos cryptos en cours'];
                    } else {
                        return ['error' => 'Transfert echoué, crypto insuffisant !'];
                    }
                    break;
                case 4:
                    $cryptoCurrent = $SoldeIndexes->getEtcSolde();
                    if ($montant <= $cryptoCurrent) {
                        //reference generer
                        $transfertCrypto = new Transfertcrypto();
                        $transfertCrypto->setFrais(0);
                        $transfertCrypto->setAmount($montant);
                        $transfertCrypto->setState(1);
                        $transfertCrypto->setValidated(0);
                        $transfertCrypto->setUpdated(new \DateTime());
                        $transfertCrypto->setCreated(new \DateTime());
                        $transfertCrypto->setUtilisateur($utilisateur);
                        $transfertCrypto->setAdressewallet($setWallet);
                        $this->em->persist($transfertCrypto);
                        $this->em->flush();

                        $newSoldeCrypto = $cryptoCurrent - $montant;
                        $utilisateur->getSoldecrypto()->setEtcSolde($newSoldeCrypto);
                        $this->em->persist($utilisateur);
                        $this->em->flush();

                        // Envoie du mail de confirmation
                        $contentData = [
                            'nom' => $utilisateur->getNom(),
                            'prenom' => $utilisateur->getPrenom(),
                            'nom_crypto' => $setWallet->getCrypto()->getName(),
                            'ref' => $utilisateur->getReference()
                        ];

                        //Admin
                        $this->mailService->sendMail(
                            'CRYPTIZY - Ordre de transfert : '.$utilisateur->getEmail().'!',
                            ['contact@cryptizy.com' => 'CRYPTIZY'],
                            ["jonathan.kablan@gmail.com", "khouiel.salah@gmail.com"],
                            $this->templating->render('email/admin/adminTransfertAlert.html.twig', $contentData)
                        );

                        //User
                        $this->mailService->sendMail(
                            'CRYPTIZY - Transfert en cours.',
                            ['contact@cryptizy.com' => 'CRYPTIZY'],
                            [$utilisateur->getEmail()],
                            $this->templating->render('email/user/transfertUser.html.twig', $contentData)
                        );

                        return ['success' => 'Transfert de vos cryptos en cours'];
                    } else {
                        return ['error' => 'Transfert echoué, crypto insuffisant !'];
                    }
                    break;
                case 5:
                    $cryptoCurrent = $SoldeIndexes->getLtcSolde();
                    if ($montant <= $cryptoCurrent) {
                        //reference generer
                        $transfertCrypto = new Transfertcrypto();
                        $transfertCrypto->setFrais(0);
                        $transfertCrypto->setAmount($montant);
                        $transfertCrypto->setState(1);
                        $transfertCrypto->setValidated(0);
                        $transfertCrypto->setUpdated(new \DateTime());
                        $transfertCrypto->setCreated(new \DateTime());
                        $transfertCrypto->setUtilisateur($utilisateur);
                        $transfertCrypto->setAdressewallet($setWallet);
                        $this->em->persist($transfertCrypto);
                        $this->em->flush();

                        $newSoldeCrypto = $cryptoCurrent - $montant;
                        $utilisateur->getSoldecrypto()->setLtcSolde($newSoldeCrypto);
                        $this->em->persist($utilisateur);
                        $this->em->flush();

                        // Envoie du mail de confirmation
                        $contentData = [
                            'nom' => $utilisateur->getNom(),
                            'prenom' => $utilisateur->getPrenom(),
                            'nom_crypto' => $setWallet->getCrypto()->getName(),
                            'ref' => $utilisateur->getReference()
                        ];

                        //Admin
                        $this->mailService->sendMail(
                            'CRYPTIZY - Ordre de transfert : '.$utilisateur->getEmail().'!',
                            ['contact@cryptizy.com' => 'CRYPTIZY'],
                            ["jonathan.kablan@gmail.com", "khouiel.salah@gmail.com"],
                            $this->templating->render('email/admin/adminTransfertAlert.html.twig', $contentData)
                        );

                        //User
                        $this->mailService->sendMail(
                            'CRYPTIZY - Transfert en cours.',
                            ['contact@cryptizy.com' => 'CRYPTIZY'],
                            [$utilisateur->getEmail()],
                            $this->templating->render('email/user/transfertUser.html.twig', $contentData)
                        );

                        return ['success' => 'Transfert de vos cryptos en cours'];
                    } else {
                        return ['error' => 'Transfert echoué, crypto insuffisant !'];
                    }
                    break;
                case 6:
                    $cryptoCurrent = $SoldeIndexes->getZecSolde();
                    if ($montant <= $cryptoCurrent) {
                        //reference generer
                        $transfertCrypto = new Transfertcrypto();
                        $transfertCrypto->setFrais(0);
                        $transfertCrypto->setAmount($montant);
                        $transfertCrypto->setState(1);
                        $transfertCrypto->setValidated(0);
                        $transfertCrypto->setUpdated(new \DateTime());
                        $transfertCrypto->setCreated(new \DateTime());
                        $transfertCrypto->setUtilisateur($utilisateur);
                        $transfertCrypto->setAdressewallet($setWallet);
                        $this->em->persist($transfertCrypto);
                        $this->em->flush();

                        $newSoldeCrypto = $cryptoCurrent - $montant;
                        $utilisateur->getSoldecrypto()->setZecSolde($newSoldeCrypto);
                        $this->em->persist($utilisateur);
                        $this->em->flush();

                        // Envoie du mail de confirmation
                        $contentData = [
                            'nom' => $utilisateur->getNom(),
                            'prenom' => $utilisateur->getPrenom(),
                            'nom_crypto' => $setWallet->getCrypto()->getName(),
                            'ref' => $utilisateur->getReference()
                        ];

                        //Admin
                        $this->mailService->sendMail(
                            'CRYPTIZY - Ordre de transfert : '.$utilisateur->getEmail().'!',
                            ['contact@cryptizy.com' => 'CRYPTIZY'],
                            ["jonathan.kablan@gmail.com", "khouiel.salah@gmail.com"],
                            $this->templating->render('email/admin/adminTransfertAlert.html.twig', $contentData)
                        );

                        //User
                        $this->mailService->sendMail(
                            'CRYPTIZY - Transfert en cours.',
                            ['contact@cryptizy.com' => 'CRYPTIZY'],
                            [$utilisateur->getEmail()],
                            $this->templating->render('email/user/transfertUser.html.twig', $contentData)
                        );

                        return ['success' => 'Transfert de vos cryptos en cours'];
                    } else {
                        return ['error' => 'Transfert echoué, crypto insuffisant !'];
                    }
                    break;
                case 7:
                    $cryptoCurrent = $SoldeIndexes->getXrpSolde();
                    if ($montant <= $cryptoCurrent) {
                        //reference generer
                        $transfertCrypto = new Transfertcrypto();
                        $transfertCrypto->setFrais(0);
                        $transfertCrypto->setAmount($montant);
                        $transfertCrypto->setState(1);
                        $transfertCrypto->setValidated(0);
                        $transfertCrypto->setUpdated(new \DateTime());
                        $transfertCrypto->setCreated(new \DateTime());
                        $transfertCrypto->setUtilisateur($utilisateur);
                        $transfertCrypto->setAdressewallet($setWallet);
                        $this->em->persist($transfertCrypto);
                        $this->em->flush();

                        // Debiter
                        $newSoldeCrypto = $cryptoCurrent - $montant;
                        $utilisateur->getSoldecrypto()->setXrpSolde($newSoldeCrypto);
                        $this->em->persist($utilisateur);
                        $this->em->flush();

                        // Envoie du mail de confirmation
                        $contentData = [
                            'nom' => $utilisateur->getNom(),
                            'prenom' => $utilisateur->getPrenom(),
                            'nom_crypto' => $setWallet->getCrypto()->getName(),
                            'ref' => $utilisateur->getReference()
                        ];

                        //Admin
                        $this->mailService->sendMail(
                            'CRYPTIZY - Ordre de transfert : '.$utilisateur->getEmail().'!',
                            ['contact@cryptizy.com' => 'CRYPTIZY'],
                            ["jonathan.kablan@gmail.com", "khouiel.salah@gmail.com"],
                            $this->templating->render('email/admin/adminTransfertAlert.html.twig', $contentData)
                        );

                        //User
                        $this->mailService->sendMail(
                            'CRYPTIZY - Transfert en cours.',
                            ['contact@cryptizy.com' => 'CRYPTIZY'],
                            [$utilisateur->getEmail()],
                            $this->templating->render('email/user/transfertUser.html.twig', $contentData)
                        );

                        return ['success' => 'Transfert de vos cryptos en cours'];
                    } else {
                        return ['error' => 'Transfert echoué, crypto insuffisant !'];
                    }
                    break;
                case 8:
                    $cryptoCurrent = $SoldeIndexes->getEosSolde();
                    if ($montant <= $cryptoCurrent) {
                        //reference generer
                        $transfertCrypto = new Transfertcrypto();
                        $transfertCrypto->setFrais(0);
                        $transfertCrypto->setAmount($montant);
                        $transfertCrypto->setState(1);
                        $transfertCrypto->setValidated(0);
                        $transfertCrypto->setUpdated(new \DateTime());
                        $transfertCrypto->setCreated(new \DateTime());
                        $transfertCrypto->setUtilisateur($utilisateur);
                        $transfertCrypto->setAdressewallet($setWallet);
                        $this->em->persist($transfertCrypto);
                        $this->em->flush();

                        // Debiter
                        $newSoldeCrypto = $cryptoCurrent - $montant;
                        $utilisateur->getSoldecrypto()->setEosSolde($newSoldeCrypto);
                        $this->em->persist($utilisateur);
                        $this->em->flush();

                        // Envoie du mail de confirmation
                        $contentData = [
                            'nom' => $utilisateur->getNom(),
                            'prenom' => $utilisateur->getPrenom(),
                            'nom_crypto' => $setWallet->getCrypto()->getName(),
                            'ref' => $utilisateur->getReference()
                        ];

                        //Admin
                        $this->mailService->sendMail(
                            'CRYPTIZY - Ordre de transfert : '.$utilisateur->getEmail().'!',
                            ['contact@cryptizy.com' => 'CRYPTIZY'],
                            ["jonathan.kablan@gmail.com", "khouiel.salah@gmail.com"],
                            $this->templating->render('email/admin/adminTransfertAlert.html.twig', $contentData)
                        );

                        //User
                        $this->mailService->sendMail(
                            'CRYPTIZY - Transfert en cours.',
                            ['contact@cryptizy.com' => 'CRYPTIZY'],
                            [$utilisateur->getEmail()],
                            $this->templating->render('email/user/transfertUser.html.twig', $contentData)
                        );

                        return ['success' => 'Transfert de vos cryptos en cours'];
                    } else {
                        return ['error' => 'Transfert echoué, crypto insuffisant !'];
                    }
                    break;
            }
        } else {
            return $data = [
                'transfertCrypto' => $AllTransfertCryptos,
                'cryptos' => $AllCryptos,
                'soldecryptos' => $SoldeCryptos,
                'solde' => $utilisateur->getSolde()->getSolde(),
                'soldeOld' => $utilisateur->getSolde()->getSoldeOld(),
                'MyAdressewallets' => $MyAdressewallet
            ];
        }

        return ['error' => 'Not found response'];
    }
}
