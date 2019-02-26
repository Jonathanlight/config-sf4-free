<?php

namespace App\Controller\User;

use App\Manager\CryptoManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class WalletController extends AbstractController
{
    /**
     * @Route("/user/add/Wallet/{_locale}", name="addWallet", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     * @Security("is_granted(constant('\\App\\Security\\Voter\\UserVoter::USER_VIEW'))")
     * @param Request $request
     * @param CryptoManager $cryptoManager
     * @return RedirectResponse
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function addWallet(Request $request, CryptoManager $cryptoManager)
    {
        $user = $this->getUser();
        if ($request->isMethod('POST')) {
            $data = $request->request->all();
            $cryptoManager->addWallet($user, $data);
        }
        return $this->redirectToRoute('transfert-crypto');
    }
}