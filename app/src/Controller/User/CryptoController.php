<?php

namespace App\Controller\User;

use App\Manager\OperationManager;
use App\Repository\CryptoRepository;
use App\Repository\OperationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class CryptoController extends AbstractController
{
    /**
     * @Route("/user/cryptos/{_locale}", name="mes_cryptos", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     * @Security("is_granted(constant('\\App\\Security\\Voter\\UserVoter::USER_VIEW'))")
     * @param Request $request
     * @param OperationRepository $operationRepository
     * @param CryptoRepository $cryptoRepository
     * @param OperationManager $operationManager
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function cryptos(
        Request $request,
        OperationRepository $operationRepository,
        CryptoRepository $cryptoRepository,
        OperationManager $operationManager
    ) {
        $user = $this->getUser();
        $transactions = $operationRepository->getOperationByUser($user);

        $tabCrypto = [];
        $varCryptos = $cryptoRepository->findAll();

        foreach ($varCryptos as $value) {
            array_push($tabCrypto, $operationRepository->getPriceByOperation($user, $value->getId()));
        }

        if ($request->isMethod('POST')) {
            $operationManager->addAdresseWallet($request->request->all());
            return $this->redirectToRoute('mes_cryptos');
        }

        return $this->render('user/crypto.html.twig', [
            'cryptos' => $transactions,
            'AllAmountByCryptos' => $tabCrypto
        ]);
    }
}
