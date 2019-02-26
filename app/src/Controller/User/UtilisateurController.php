<?php

namespace App\Controller\User;

use App\Entity\Depot;
use App\Entity\Utilisateur;
use App\Manager\CryptoManager;
use App\Manager\SellManager;
use App\Repository\CryptoRepository;
use App\Repository\DepotRepository;
use App\Repository\OperationRepository;
use App\Repository\ParainageRepository;
use App\Services\MessageService;
use Swagger\Annotations\Header;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;

class UtilisateurController extends AbstractController
{
    /**
     * @Route("/user/{_locale}/", name="dashboard_user", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     * @param CryptoManager $cryptoManager
     * @param OperationRepository $operationRepository
     * @param CryptoRepository $cryptoRepository
     * @param ParainageRepository $parainageRepository
     * @param SellManager $sellManager
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function officeHome(
        CryptoManager $cryptoManager,
        OperationRepository $operationRepository,
        CryptoRepository $cryptoRepository,
        ParainageRepository $parainageRepository,
        SellManager $sellManager
    ) {
        //Verification du lien de parainage
        $tabCrypto = [];
        $user = $this->getUser();
        $cryptoManager->parainnage($parainageRepository->getCheckParainUser($user), $user);

        foreach ($cryptoRepository->findAll() as $value) {
            array_push($tabCrypto, $operationRepository->getPriceByOperation($user, $value->getId()));
        }

        return $this->render('user/index.html.twig', [
            'valid' => $user->getValidated(),
            'transactions' => $operationRepository->getOperationByUser($user),
            'solde' => round($user->getSolde()->getSolde(), 2),
            'soldeOld' => round($user->getSolde()->getSoldeOld(), 2),
            'totalDepose' => round($user->getSolde()->getSoldeOld() - $sellManager->getAllSumVirementByUser($user), 2),
            'AllAmountByCryptos' => $tabCrypto,
            'soldecryptos' => $user->getSoldecrypto()
        ]);
    }

    /**
     * @Route("/user/graph/{id}/{_locale}", name="graph", defaults={"_locale" = "fr"})
     * @Security("is_granted(constant('\\App\\Security\\Voter\\UserVoter::USER_VIEW'))")
     * @param Utilisateur $user
     * @param DepotRepository $depotRepository
     * @return JsonResponse
     */
    public function graphique(Utilisateur $user, DepotRepository $depotRepository)
    {
        $getInfo = $depotRepository->getDepotByUser($user);
        $newFormat = [];
        //Réecris le format JSON pour le plugin du graphique
        foreach ($getInfo as $value) {
            if ($value->getCreated() !== null) {
                array_push($newFormat, [
                    'date' => date_format($value->getCreated(), 'Y-m-d') ,
                    'value' => $value->getMontant()
                ]);
            }
        }

        return new JsonResponse($newFormat, JsonResponse::HTTP_OK);
    }

    /**
     * @Route("/user/compte/{_locale}", name="compte", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     * @Security("is_granted(constant('\\App\\Security\\Voter\\UserVoter::USER_VIEW'))")
     * @param Request $request
     * @param CryptoManager $cryptoManager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function accompte(Request $request, CryptoManager $cryptoManager)
    {
        $user = $this->getUser();
        if ($request->isMethod('POST')) {
            $data = $request->request->all();
            $cryptoManager->editTelephone($user, $data);
        }

        return $this->render('user/compte.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Route("/user/updatepassword/{_locale}", name="updatepassword", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     * @Security("is_granted(constant('\\App\\Security\\Voter\\UserVoter::USER_VIEW'))")
     * @param Request $request
     * @param CryptoManager $cryptoManager
     * @return RedirectResponse
     */
    public function updatePassword(Request $request, CryptoManager $cryptoManager)
    {
        $user = $this->getUser();
        if ($request->isMethod('POST')) {
            $cryptoManager->updatePassword($user, $request->request->all());
        }

        return $this->redirectToRoute('compte');
    }

    /**
     * @Route("/user/reservation/{_locale}", name="reservation", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     * @param Request $request
     * @param CryptoRepository $cryptoRepository
     * @param ParainageRepository $parainageRepository
     * @param CryptoManager $cryptoManager
     * @param MessageService $messageService
     * @param SellManager $sellManager
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function reservation(
        Request $request,
        CryptoRepository $cryptoRepository,
        ParainageRepository $parainageRepository,
        CryptoManager $cryptoManager,
        MessageService $messageService,
        SellManager $sellManager
    ) {
        $AllCryptos = $cryptoRepository->findAll();
        $user = $this->getUser();
        //Verification du lien de parainage
        $cryptoManager->parainnage($parainageRepository->getCheckParainUser($user), $user);

        if ($request->isMethod('POST')) {
            if ($user->getValidated() == 1) {
                $cryptoManager->newOperation($user, $request->request->all());
            } else {
                $messageService->addError('Votre profil doit être vérifié pour soumettre des ordres. Allez dans Vérification pour faire vérifier votre compte.');
            }
        }

        return $this->render('user/reservation.html.twig', [
            'cryptos' => $AllCryptos,
            'solde' => round($user->getSolde()->getSolde(), 2),
            'soldeOld' => round($user->getSolde()->getSoldeOld(), 2),
            'totalDepose' => round($user->getSolde()->getSoldeOld() - $sellManager->getAllSumVirementByUser($user), 2)
        ]);
    }



    /**
     * @Route("/user/verif/{_locale}", name="verif", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     * @Security("is_granted(constant('\\App\\Security\\Voter\\UserVoter::USER_VIEW'))")
     * @param Request $request
     * @param CryptoManager $cryptoManager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function verification(Request $request, CryptoManager $cryptoManager)
    {
        $user = $this->getUser();
        if ($request->isMethod('POST')) {
            $data = $request->request->all();
            if ($user instanceof Utilisateur) {
                $cryptoManager->verif($user, $data);
            }
        }
        return $this->render('user/verif.html.twig', []);
    }

    /**
     * @Route("/user/depot/{_locale}", name="depot_user", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     * @Security("is_granted(constant('\\App\\Security\\Voter\\UserVoter::USER_VIEW'))")
     * @param DepotRepository $depotRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function depot_userAction(DepotRepository $depotRepository)
    {
        $depotUser = $depotRepository->getDepotByUser($this->getUser());

        return $this->render('user/depot_user.html.twig', [
            'depotUser' => $depotUser
        ]);
    }

    /**
     * @Route("/user/add/iban/{_locale}", name="addIban", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     * @Security("is_granted(constant('\\App\\Security\\Voter\\UserVoter::USER_VIEW'))")
     * @param Request $request
     * @param CryptoManager $cryptoManager
     * @return RedirectResponse
     */
    public function addIban(Request $request, CryptoManager $cryptoManager)
    {
        if ($request->isMethod('POST')) {
            $data = $request->request->all();
            $cryptoManager->addIban($this->getUser(), $data);
        }

        return $this->redirectToRoute('compte');
    }

    /**
     * @Route("/user/affiliation/{_locale}", name="affiliation-user", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     * @Security("is_granted(constant('\\App\\Security\\Voter\\UserVoter::USER_VIEW'))")
     * @param ParainageRepository $parainageRepository
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function affiliation(ParainageRepository $parainageRepository)
    {
        $user = $this->getUser();
        return $this->render('user/parrainage.html.twig', [
            'parrainage' => $parainageRepository->getStorieParainUser($user),
            'totalFilleul' => $parainageRepository->getAllFilleulByUser($user),
            'totalSolde' => $parainageRepository->getAllSoldeParrainageByUser($user)
        ]);
    }
}
