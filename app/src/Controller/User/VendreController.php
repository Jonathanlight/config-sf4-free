<?php

namespace App\Controller\User;

use App\Entity\Crypto;
use App\Manager\CryptoManager;
use App\Manager\SellManager;
use App\Repository\CryptoRepository;
use App\Repository\OperationRepository;
use App\Repository\VendreRepository;
use App\Services\MessageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class VendreController extends AbstractController
{
    /**
     * @Route("/user/sell/crypto/{_locale}", name="sellcrypto-user", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     * @Security("is_granted(constant('\\App\\Security\\Voter\\UserVoter::USER_VIEW'))")
     * @param Request $request
     * @param CryptoManager $cryptoManager
     * @param CryptoRepository $cryptoRepository
     * @param MessageService $messageService
     * @return RedirectResponse
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sellCrypto(
        Request $request,
        CryptoManager $cryptoManager,
        CryptoRepository $cryptoRepository,
        MessageService $messageService
    ) {
        $user = $this->getUser();
        if ($request->isMethod('POST')) {
            $data = $request->request->all();
            $crypto = trim($data['crypto']);
            $soldeCurrent = $user->getSolde()->getSolde();

            $id = $data['target'];
            $stateVente = $data['stateVente'];
            $cryptoCurrent = 0;
            $CryptoAdd = $cryptoRepository->find($id);
            $SoldeIndexes = $user->getSoldecrypto();

            if (!$CryptoAdd instanceof Crypto) {
                $CryptoAdd = null;
            }

            switch ($id) {
                case 1:
                    $cryptoCurrent = $SoldeIndexes->getBtcSolde();
                    if ($crypto <= $cryptoCurrent) {
                        $cryptoManager->sellCrypto($user, $CryptoAdd, $stateVente, $crypto);
                        $newSoldeCrypto = $cryptoCurrent - $crypto;
                        $cryptoManager->debitCryptoBtcSolde($user, $CryptoAdd, $newSoldeCrypto, $crypto);
                    } else {
                        $messageService->addError('Votre Solde est insuffisant pour soumettre l\'ordre.');
                    }
                    break;
                case 2:
                    $cryptoCurrent = $SoldeIndexes->getBchSolde();
                    if ($crypto <= $cryptoCurrent) {
                        $cryptoManager->sellCrypto($user, $CryptoAdd, $stateVente, $crypto);
                        $newSoldeCrypto = $cryptoCurrent - $crypto;
                        $cryptoManager->debitCryptoBchSolde($user, $CryptoAdd, $newSoldeCrypto, $crypto);
                    } else {
                        $messageService->addError('Votre Solde est insuffisant pour soumettre l\'ordre.');
                    }
                    break;
                case 3:
                    $cryptoCurrent = $SoldeIndexes->getEthSolde();
                    if ($crypto <= $cryptoCurrent) {
                        $cryptoManager->sellCrypto($user, $CryptoAdd, $stateVente, $crypto);
                        $newSoldeCrypto = $cryptoCurrent - $crypto;
                        $cryptoManager->debitCryptoEthSolde($user, $CryptoAdd, $newSoldeCrypto, $crypto);
                    } else {
                        $messageService->addError('Votre Solde est insuffisant pour soumettre l\'ordre.');
                    }
                    break;
                case 4:
                    $cryptoCurrent = $SoldeIndexes->getEtcSolde();
                    if ($crypto <= $cryptoCurrent) {
                        $cryptoManager->sellCrypto($user, $CryptoAdd, $stateVente, $crypto);
                        $newSoldeCrypto = $cryptoCurrent - $crypto;
                        $cryptoManager->debitCryptoEtcSolde($user, $CryptoAdd, $newSoldeCrypto, $crypto);
                    } else {
                        $messageService->addError('Votre Solde est insuffisant pour soumettre l\'ordre.');
                    }
                    break;
                case 5:
                    $cryptoCurrent = $SoldeIndexes->getLtcSolde();
                    if ($crypto <= $cryptoCurrent) {
                        $cryptoManager->sellCrypto($user, $CryptoAdd, $stateVente, $crypto);
                        $newSoldeCrypto = $cryptoCurrent - $crypto;
                        $cryptoManager->debitCryptoLtcSolde($user, $CryptoAdd, $newSoldeCrypto, $crypto);
                    } else {
                        $messageService->addError('Votre Solde est insuffisant pour soumettre l\'ordre.');
                    }
                    break;
                case 6:
                    $cryptoCurrent = $SoldeIndexes->getZecSolde();
                    if ($crypto <= $cryptoCurrent) {
                        $cryptoManager->sellCrypto($user, $CryptoAdd, $stateVente, $crypto);
                        $newSoldeCrypto = $cryptoCurrent - $crypto;
                        $cryptoManager->debitCryptoZecSolde($user, $CryptoAdd, $newSoldeCrypto, $crypto);
                    } else {
                        $messageService->addError('Votre Solde est insuffisant pour soumettre l\'ordre.');
                    }
                    break;
                case 7:
                    $cryptoCurrent = $SoldeIndexes->getXrpSolde();
                    if ($crypto <= $cryptoCurrent) {
                        $cryptoManager->sellCrypto($user, $CryptoAdd, $stateVente, $crypto);
                        $newSoldeCrypto = $cryptoCurrent - $crypto;
                        $cryptoManager->debitCryptoXrpSolde($user, $CryptoAdd, $newSoldeCrypto, $crypto);
                    } else {
                        $messageService->addError('Votre Solde est insuffisant pour soumettre l\'ordre.');
                    }
                    break;
                case 8:
                    $cryptoCurrent = $SoldeIndexes->getEosSolde();
                    if ($crypto <= $cryptoCurrent) {
                        $cryptoManager->sellCrypto($user, $CryptoAdd, $stateVente, $crypto);
                        $newSoldeCrypto = $cryptoCurrent - $crypto;
                        $cryptoManager->debitCryptoEosSolde($user, $CryptoAdd, $newSoldeCrypto, $crypto);
                    } else {
                        $messageService->addError('Votre Solde est insuffisant pour soumettre l\'ordre.');
                    }
                    break;
            }
        }

        return $this->redirectToRoute('sell');
    }

    /**
     * @Route("/user/sell/{_locale}", name="sell", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     * @param OperationRepository $operationRepository
     * @param SellManager $sellManager
     * @param CryptoRepository $cryptoRepository
     * @param VendreRepository $vendreRepository
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function sell(
        OperationRepository $operationRepository,
        SellManager $sellManager,
        CryptoRepository $cryptoRepository,
        VendreRepository $vendreRepository
    ) {

        $tabCrypto = [];
        $user = $this->getUser();
        $varCryptos = $cryptoRepository->findAll();

        foreach ($varCryptos as $value) {
            array_push($tabCrypto, $operationRepository->getPriceByOperation($user, $value->getId()));
        }

        $SoldeCryptos = $user->getSoldecrypto();
        $AllCryptos = $cryptoRepository->findAll();
        $transactionsSell = $vendreRepository->getVenteByUser($user);

        return $this->render('user/sell.html.twig', [
            'cryptos' => $AllCryptos,
            'solde' => round($user->getSolde()->getSolde(), 2),
            'soldeOld' => round($user->getSolde()->getSoldeOld(), 2),
            'totalDepose' => round($user->getSolde()->getSoldeOld() - $sellManager->getAllSumVirementByUser($user), 2),
            'AllAmountByCryptos' => $tabCrypto,
            'soldecryptos' => $SoldeCryptos,
            'historiqueVentes' => $transactionsSell
        ]);
    }
}
