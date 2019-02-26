<?php

namespace App\Controller\User;

use App\Entity\Crypto;
use App\Entity\Utilisateur;
use App\Manager\CryptoManager;
use App\Manager\SellManager;
use App\Repository\AdressewalletRepository;
use App\Repository\CryptoRepository;
use App\Repository\FriendsRepository;
use App\Repository\TransfertcryptoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class TransfertController extends AbstractController
{
    /**
     * @Route("/user/transfert/crypto/{_locale}", name="transfert-crypto", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     * @param Request $request
     * @param CryptoManager $cryptoManager
     * @param TransfertcryptoRepository $transfertcryptoRepository
     * @param CryptoRepository $cryptoRepository
     * @param AdressewalletRepository $adressewalletRepository
     * @param SellManager $sellManager
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function transfertCrypto(
        Request $request,
        CryptoManager $cryptoManager,
        TransfertcryptoRepository $transfertcryptoRepository,
        CryptoRepository $cryptoRepository,
        AdressewalletRepository $adressewalletRepository,
        SellManager $sellManager
    ) {
        $user = $this->getUser();
        $SoldeCryptos = $user->getSoldecrypto();
        $AllCryptos = $cryptoRepository->findAll();
        $AllTransfertCryptos = $transfertcryptoRepository->getTransfertcryptoByUser($user);
        $MyAdressewallet = $adressewalletRepository->getByUser($user);

        if ($request->isMethod('POST')) {
            $data = $request->request->all();

            $montant = trim($data['montant']);
            $crypto_id = trim($data['crypto_id']);
            $wallet_id = trim($data['wallet']);

            $setWallet = $adressewalletRepository->find($wallet_id);
            $crypto = $cryptoRepository->find($crypto_id);

            $SoldeIndexes = $user->getSoldecrypto();
            switch ($crypto_id) {
                case 1:
                    $cryptoManager->transfertCrypto($user, $SoldeIndexes->getBtcSolde(), $montant, $setWallet, $crypto);
                    break;
                case 2:
                    $cryptoManager->transfertCrypto($user, $SoldeIndexes->getBchSolde(), $montant, $setWallet, $crypto);
                    break;
                case 3:
                    $cryptoManager->transfertCrypto($user, $SoldeIndexes->getEthSolde(), $montant, $setWallet, $crypto);
                    break;
                case 4:
                    $cryptoManager->transfertCrypto($user, $SoldeIndexes->getEtcSolde(), $montant, $setWallet, $crypto);
                    break;
                case 5:
                    $cryptoManager->transfertCrypto($user, $SoldeIndexes->getLtcSolde(), $montant, $setWallet, $crypto);
                    break;
                case 6:
                    $cryptoManager->transfertCrypto($user, $SoldeIndexes->getZecSolde(), $montant, $setWallet, $crypto);
                    break;
                case 7:
                    $cryptoManager->transfertCrypto($user, $SoldeIndexes->getXrpSolde(), $montant, $setWallet, $crypto);
                    break;
                case 8:
                    $cryptoManager->transfertCrypto($user, $SoldeIndexes->getEosSolde(), $montant, $setWallet, $crypto);
                    break;
            }
            return $this->redirectToRoute('transfert-crypto');
        }

        return $this->render('user/transfertCrypto.html.twig', [
            'transfertCrypto' => $AllTransfertCryptos,
            'cryptos' => $AllCryptos,
            'soldecryptos' => $SoldeCryptos,
            'solde' => round($user->getSolde()->getSolde(), 2),
            'soldeOld' => round($user->getSolde()->getSoldeOld(), 2),
            'totalDepose' => round($user->getSolde()->getSoldeOld() - $sellManager->getAllSumVirementByUser($user), 2),
            'MyAdressewallets' => $MyAdressewallet
        ]);
    }

    /**
     * @Route("/user/transfert/ami/{_locale}", name="transfert-ami", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     * @param Request $request
     * @param CryptoManager $cryptoManager
     * @param FriendsRepository $friendsRepository
     * @param SellManager $sellManager
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function transfertFriend(
        Request $request,
        CryptoManager $cryptoManager,
        FriendsRepository $friendsRepository,
        SellManager $sellManager
    ) {
        $user = $this->getUser();
        if ($request->isMethod('POST')) {
            $data = $request->request->all();
            $montant    = trim($data['montant']);
            $crypto_id  = trim($data['crypto_id']);
            $friend     = trim($data['friend']);

            $userFriendRepository = $this->getDoctrine()->getRepository(Utilisateur::class);
            $FriendUser = $userFriendRepository->find($friend);
            $cryptoChoose = $cryptoManager->findCrypto($crypto_id);
            $SoldeIndexes = $user->getSoldecrypto();

            switch ($crypto_id) {
                case 1:
                    $cryptoCurrent = $SoldeIndexes->getBtcSolde();
                    if ($montant <= $cryptoCurrent) {
                        if ($user instanceof Utilisateur) {
                            if ($FriendUser instanceof Utilisateur) {
                                if ($cryptoChoose instanceof Crypto) {
                                    $cryptoManager->transfertFriend($user, $montant, $FriendUser, $cryptoChoose);
                                }
                            }
                        }
                        $newSoldeCrypto = $cryptoCurrent - $montant;
                        $cryptoManager->setBtcSolde($user, $newSoldeCrypto);
                        $newSoldeCrypto = $FriendUser->getSoldecrypto()->getBtcSolde() + $montant;
                        $cryptoManager->creditBtcSolde($FriendUser, $newSoldeCrypto);
                    } else {
                        $cryptoManager->errorTransfert('Transfert echoué, crypto insuffisant !');
                    }
                    break;
                case 2:
                    $cryptoCurrent = $SoldeIndexes->getBchSolde();
                    if ($montant <= $cryptoCurrent) {
                        if ($user instanceof Utilisateur) {
                            if ($FriendUser instanceof Utilisateur) {
                                if ($cryptoChoose instanceof Crypto) {
                                    $cryptoManager->transfertFriend($user, $montant, $FriendUser, $cryptoChoose);
                                }
                            }
                        }
                        $newSoldeCrypto = $cryptoCurrent - $montant;
                        $cryptoManager->setBchSolde($user, $newSoldeCrypto);
                        $newSoldeCrypto = $FriendUser->getSoldecrypto()->getBchSolde() + $montant;
                        $cryptoManager->creditBchSolde($FriendUser, $newSoldeCrypto);
                    } else {
                        $cryptoManager->errorTransfert('Transfert echoué, crypto insuffisant !');
                    }
                    break;
                case 3:
                    $cryptoCurrent = $SoldeIndexes->getEthSolde();
                    if ($montant <= $cryptoCurrent) {
                        if ($user instanceof Utilisateur) {
                            if ($FriendUser instanceof Utilisateur) {
                                if ($cryptoChoose instanceof Crypto) {
                                    $cryptoManager->transfertFriend($user, $montant, $FriendUser, $cryptoChoose);
                                }
                            }
                        }
                        $newSoldeCrypto = $cryptoCurrent - $montant;
                        $cryptoManager->setEthSolde($user, $newSoldeCrypto);
                        $newSoldeCrypto = $FriendUser->getSoldecrypto()->getEthSolde() + $montant;
                        $cryptoManager->creditEthSolde($FriendUser, $newSoldeCrypto);
                    } else {
                        $cryptoManager->errorTransfert('Transfert echoué, crypto insuffisant !');
                    }
                    break;
                case 4:
                    $cryptoCurrent = $SoldeIndexes->getEtcSolde();
                    if ($montant <= $cryptoCurrent) {
                        if ($user instanceof Utilisateur) {
                            if ($FriendUser instanceof Utilisateur) {
                                if ($cryptoChoose instanceof Crypto) {
                                    $cryptoManager->transfertFriend($user, $montant, $FriendUser, $cryptoChoose);
                                }
                            }
                        }
                        $newSoldeCrypto = $cryptoCurrent - $montant;
                        $cryptoManager->setEtcSolde($user, $newSoldeCrypto);
                        $newSoldeCrypto = $FriendUser->getSoldecrypto()->getEtcSolde() + $montant;
                        $cryptoManager->creditEtcSolde($FriendUser, $newSoldeCrypto);
                    } else {
                        $cryptoManager->errorTransfert('Transfert echoué, crypto insuffisant !');
                    }
                    break;
                case 5:
                    $cryptoCurrent = $SoldeIndexes->getLtcSolde();
                    if ($montant <= $cryptoCurrent) {
                        if ($user instanceof Utilisateur) {
                            if ($FriendUser instanceof Utilisateur) {
                                if ($cryptoChoose instanceof Crypto) {
                                    $cryptoManager->transfertFriend($user, $montant, $FriendUser, $cryptoChoose);
                                }
                            }
                        }
                        $newSoldeCrypto = $cryptoCurrent - $montant;
                        $cryptoManager->setLtcSolde($user, $newSoldeCrypto);
                        $newSoldeCrypto = $FriendUser->getSoldecrypto()->getLtcSolde() + $montant;
                        $cryptoManager->creditLtcSolde($FriendUser, $newSoldeCrypto);
                    } else {
                        $cryptoManager->errorTransfert('Transfert echoué, crypto insuffisant !');
                    }
                    break;
                case 6:
                    $cryptoCurrent = $SoldeIndexes->getZecSolde();
                    if ($montant <= $cryptoCurrent) {
                        if ($user instanceof Utilisateur) {
                            if ($FriendUser instanceof Utilisateur) {
                                if ($cryptoChoose instanceof Crypto) {
                                    $cryptoManager->transfertFriend($user, $montant, $FriendUser, $cryptoChoose);
                                }
                            }
                        }
                        $newSoldeCrypto = $cryptoCurrent - $montant;
                        $cryptoManager->setZecSolde($user, $newSoldeCrypto);
                        $newSoldeCrypto = $FriendUser->getSoldecrypto()->getZecSolde() + $montant;
                        $cryptoManager->creditZecSolde($FriendUser, $newSoldeCrypto);
                    } else {
                        $cryptoManager->errorTransfert('Transfert echoué, crypto insuffisant !');
                    }
                    break;
                case 7:
                    $cryptoCurrent = $SoldeIndexes->getXrpSolde();
                    if ($montant <= $cryptoCurrent) {
                        if ($user instanceof Utilisateur) {
                            if ($FriendUser instanceof Utilisateur) {
                                if ($cryptoChoose instanceof Crypto) {
                                    $cryptoManager->transfertFriend($user, $montant, $FriendUser, $cryptoChoose);
                                }
                            }
                        }
                        $newSoldeCrypto = $cryptoCurrent - $montant;
                        $cryptoManager->setXrpSolde($user, $newSoldeCrypto);
                        $newSoldeCrypto = $FriendUser->getSoldecrypto()->getXrpSolde() + $montant;
                        $cryptoManager->creditXrpSolde($FriendUser, $newSoldeCrypto);
                    } else {
                        $cryptoManager->errorTransfert('Transfert echoué, crypto insuffisant !');
                    }
                    break;
                case 8:
                    $cryptoCurrent = $SoldeIndexes->getEosSolde();
                    if ($montant <= $cryptoCurrent) {
                        if ($user instanceof Utilisateur) {
                            if ($FriendUser instanceof Utilisateur) {
                                if ($cryptoChoose instanceof Crypto) {
                                    $cryptoManager->transfertFriend($user, $montant, $FriendUser, $cryptoChoose);
                                }
                            }
                        }
                        $newSoldeCrypto = $cryptoCurrent - $montant;
                        $cryptoManager->setEosSolde($user, $newSoldeCrypto);
                        $newSoldeCrypto = $FriendUser->getSoldecrypto()->getEosSolde() + $montant;
                        $cryptoManager->creditEosSolde($FriendUser, $newSoldeCrypto);
                    } else {
                        $cryptoManager->errorTransfert('Transfert echoué, crypto insuffisant !');
                    }
                    break;
            }
        }
        return $this->render('user/transfertFriend.html.twig', [
            'cryptos' => $cryptoManager->allCryptos(),
            'soldecryptos' => $user->getSoldecrypto(),
            'solde' => round($user->getSolde()->getSolde(),2),
            'soldeOld' => round($user->getSolde()->getSoldeOld(),2),
            'totalDepose' => round($user->getSolde()->getSoldeOld() - $sellManager->getAllSumVirementByUser($user), 2),
            'allFriends' => $friendsRepository->getFriendByUser($user)
        ]);
    }
}
