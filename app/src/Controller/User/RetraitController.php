<?php

namespace App\Controller\User;

use App\Manager\CryptoManager;
use App\Manager\SellManager;
use App\Repository\RetraitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class RetraitController extends AbstractController
{
    /**
     * @Route("/user/retrait/user/{_locale}", name="retrait-user", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     * @param Request $request
     * @param CryptoManager $cryptoManager
     * @param RetraitRepository $retraitRepository
     * @param SellManager $sellManager
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function retrait(
        Request $request,
        CryptoManager $cryptoManager,
        RetraitRepository $retraitRepository,
        SellManager $sellManager
    ) {
        $transfert = $retraitRepository->getTransfertByUser($this->getUser());

        if ($request->isMethod('POST')) {
            $data = $request->request->all();
            $solde = trim($data['solde']);
            $cryptoManager->retrait($this->getUser(), $this->getUser()->getSolde()->getSolde(), $solde);
        }

        return $this->render('user/retrait.html.twig', [
            'user'=>$this->getUser(),
            'transferts' => $transfert,
            'solde' => round($this->getUser()->getSolde()->getSolde(),2),
            'soldeOld' => round($this->getUser()->getSolde()->getSoldeOld(),2),
            'totalDepose' => round($this->getUser()->getSolde()->getSoldeOld() - $sellManager->getAllSumVirementByUser($this->getUser()), 2)
        ]);
    }
}
