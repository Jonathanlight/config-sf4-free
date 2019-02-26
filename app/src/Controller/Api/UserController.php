<?php

namespace App\Controller\Api;

use App\Entity\Parainage;
use App\Entity\Utilisateur;
use App\Manager\AdressewalletManager;
use App\Manager\Api\ParainageManager;
use App\Manager\Api\UtilisateurManager;
use App\Manager\DepotManager;
use App\Manager\Api\OperationManager;
use App\Repository\CryptoRepository;
use App\Repository\RetraitRepository;
use App\Repository\UtilisateurRepository;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpKernel\Exception\HttpException;

#Nelmio component
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;

/**
 * @Route("/api/v1")
 */
class UserController extends FOSRestController
{
    /**
     * @Route("/login_check", name="user_login_check")
     * @SWG\Response(
     *     response=200,
     *     description="User was logged in successfully"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="User was not logged in successfully"
     * )
     *
     * @SWG\Parameter(
     *     name="_username",
     *     in="body",
     *     type="string",
     *     description="The username",
     *     schema={
     *     }
     * )
     *
     * @SWG\Parameter(
     *     name="_password",
     *     in="body",
     *     type="string",
     *     description="The password",
     *     schema={}
     * )
     *
     * @SWG\Tag(name="Utilisateur")
     */
    public function getLoginCheckAction()
    {

    }

    /**
     * Modifier le mot de passe d'un utilisateur par identifient
     * @Route("/update/profil/{id}", name="profil", methods={"POST"})
     * @SWG\Response(
     *     response=200,
     *     description="Modifier un profil user"
     * )
     * @SWG\Parameter(
     *     name="nom",
     *     in="query",
     *     type="string",
     *     description="Return string"
     * )
     * @SWG\Parameter(
     *     name="prenom",
     *     in="query",
     *     type="string",
     *     description="Return string"
     * )
     * @SWG\Parameter(
     *     name="telephone",
     *     in="query",
     *     type="string",
     *     description="Return string"
     * )
     * @SWG\Tag(name="Utilisateur")
     * @param Utilisateur $utilisateur
     * @param Request $request
     * @param UtilisateurManager $utilisateurManager
     * @return Response
     */
    public function putUpdateUserAction(Utilisateur $utilisateur, Request $request, UtilisateurManager $utilisateurManager)
    {
        if (!$utilisateur instanceof Utilisateur) {
            return $this->handleView($this->view(['error' => "user not found"]));
        }

        $view = $this->view($utilisateurManager->updateProfil($utilisateur, $request->request->all()));

        return $this->handleView($view);
    }

    /**
     * Modifier le mot de passe d'un utilisateur par identifient
     * @Route("/update/password/{id}", name="password", methods={"POST"})
     * @SWG\Response(
     *     response=200,
     *     description="Modifier le mot de passe d'un utilisateur par identifient"
     * )
     * @SWG\Parameter(
     *     name="password",
     *     in="query",
     *     type="string",
     *     description="Return string"
     * )
     * @SWG\Parameter(
     *     name="new_password",
     *     in="query",
     *     type="string",
     *     description="Return string"
     * )
     * @SWG\Parameter(
     *     name="confirm_new_password",
     *     in="query",
     *     type="string",
     *     description="Return string"
     * )
     * @SWG\Tag(name="Utilisateur")
     * @param Utilisateur $utilisateur
     * @param Request $request
     * @param UtilisateurManager $utilisateurManager
     * @return Response
     */
    public function putUpdatePasswordUserAction(Utilisateur $utilisateur, Request $request,UtilisateurManager $utilisateurManager)
    {
        if (!$utilisateur instanceof Utilisateur) {
            return $this->handleView($this->view(['error' => "user not found"]));
        }

        $view = $this->view($utilisateurManager->updatePassword($utilisateur, $request->request->all()));

        return $this->handleView($view);
    }

    /**
     * Recherche un utilisateur par l'identifient
     * @Route("/user/{id}", name="user", methods={"GET"})
     * @SWG\Response(
     *     response=200,
     *     description="Recherche un utilisateur par l'identifient"
     * )
     * @SWG\Tag(name="Utilisateur")
     * @param Utilisateur $utilisateur
     * @return Response
     */
    public function getUserAction(Utilisateur $utilisateur)
    {
        if (!$utilisateur instanceof Utilisateur) {
            return $this->handleView($this->view(['error' => "user not found"]));
        }

        return $this->handleView($this->view($utilisateur));
    }

    /**
     * Affiche tout les wallet d'un utilisateur
     * @Route("/wallet/{id}", name="wallet", methods={"GET"})
     * @SWG\Response(
     *     response=200,
     *     description="Affiche tout les wallet d'un utilisateur"
     * )
     * @SWG\Tag(name="Utilisateur")
     * @param Utilisateur $utilisateur
     * @param AdressewalletManager $adressewalletManager
     * @return Response
     */
    public function getWalletAction(Utilisateur $utilisateur, AdressewalletManager $adressewalletManager)
    {
        if (!$utilisateur instanceof Utilisateur) {
            return $this->handleView($this->view(['error' => "user not found"]));
        }

        return $this->handleView($this->view($adressewalletManager->adresseWalletByUser($utilisateur)));
    }

    /**
     * Liste des cryptos
     * @Route("/cryptos", name="cryptos", methods={"GET"})
     * @SWG\Response(
     *     response=200,
     *     description="Liste des cryptos"
     * )
     * @SWG\Tag(name="Utilisateur")
     * @param CryptoRepository $cryptoRepository
     * @return Response
     */
    public function getCryptoAction(CryptoRepository $cryptoRepository)
    {
        return $this->handleView($this->view($cryptoRepository->findAll()));
    }

    /**
     * Permet de connecté un utilisateur a la plate-forme
     * @Route("/connexion", name="connexion", methods={"POST"})
     * @SWG\Response(
     *     response=200,
     *     description="Permet de connecté un utilisateur a la plate-forme"
     * )
     * @SWG\Parameter(
     *     name="email",
     *     in="query",
     *     type="string",
     *     description="Return string"
     * )
     * @SWG\Parameter(
     *     name="password",
     *     in="query",
     *     type="string",
     *     description="Return string"
     * )
     * @SWG\Tag(name="Utilisateur")
     * @param Request $request
     * @param UtilisateurRepository $utilisateurRepository
     * @return Response
     */
    public function postConnexionAction(Request $request, UtilisateurRepository $utilisateurRepository)
    {
        $email = $request->request->get('email');
        $password = $request->request->get('password');

        if (isset($email) && isset($password)) {
            $restresult = $utilisateurRepository->findBy(['email' => $email]);
            if ($restresult) {
                $pass = $restresult[0]->getPassword();
                if (password_verify($password, $pass)) {
                    return $this->handleView($this->view($restresult[0]));
                } else {
                    return $this->handleView($this->view(['error' => "Password Invalid"]));
                }
            } else {
                return $this->handleView($this->view(['error' => "Email did not exist"]));
            }
        }

        return $this->handleView($this->view(['error' => "Email or passwors is empty"]));
    }

    /**
     * Route pour inscrire un utilisateur
     * @Route("/inscription", name="inscription", methods={"POST"})
     * @SWG\Response(
     *     response=200,
     *     description="Route pour inscrire un utilisateur"
     * )
     * @SWG\Parameter(
     *     name="username",
     *     in="query",
     *     type="string",
     *     description="Return string"
     * )
     * @SWG\Parameter(
     *     name="password",
     *     in="query",
     *     type="string",
     *     description="Return string"
     * )
     * @SWG\Parameter(
     *     name="email",
     *     in="query",
     *     type="string",
     *     description="Return string"
     * )
     * @SWG\Parameter(
     *     name="phone",
     *     in="query",
     *     type="string",
     *     description="Return string"
     * )
     * @SWG\Tag(name="Utilisateur")
     * @param Request $request
     * @param UtilisateurManager $utilisateurManager
     * @return Response
     */
    public function postInscriptionAction(
        Request $request,
        UtilisateurManager $utilisateurManager
    ) {
        $view = $this->view($utilisateurManager->postInscription($request->request->all()));

        return $this->handleView($view);
    }

    /**
     * Route pour acheter une crypto monnaie"
     * @Route("/reservation/{id}", name="inscription", methods={"POST"})
     * @SWG\Response(
     *     response=200,
     *     description="Route pour acheter une crypto monnaie"
     * )
     * @SWG\Parameter(
     *     name="cryptoAmount",
     *     in="query",
     *     type="string",
     *     description="Return string"
     * )
     * @SWG\Parameter(
     *     name="targetCrypto",
     *     in="query",
     *     type="string",
     *     description="Return string"
     * )
     * @SWG\Tag(name="Utilisateur")
     * @param Utilisateur $utilisateur
     * @param Request $request
     * @param ParainageManager $parainageManager
     * @param OperationManager $operationManager
     * @return Response
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function postReservationAction(
        Utilisateur $utilisateur,
        Request $request,
        ParainageManager $parainageManager,
        OperationManager $operationManager
    ) {
        //check link of sponsorship
        $parainageManager->checkParrainage($utilisateur);

        if ($request->getMethod() == "POST") {

            $view = $this->view($operationManager->reservationCrypto($utilisateur, $request->request->all()));

            return $this->handleView($view);
        }

        return $this->handleView($this->view(['error' => 'Not found response']));
    }

    /**
     * Route pour ajouter une adresse wallet
     * @Route("/add/wallet/{id}", name="addwallet", methods={"POST"})
     * @SWG\Response(
     *     response=200,
     *     description="Route pour ajouter une adresse wallet"
     * )
     * @SWG\Parameter(
     *     name="crypto_id",
     *     in="query",
     *     type="string",
     *     description="Return string"
     * )
     * @SWG\Parameter(
     *     name="adresse",
     *     in="query",
     *     type="string",
     *     description="Return string"
     * )
     * @SWG\Parameter(
     *     name="description",
     *     in="query",
     *     type="string",
     *     description="Return string"
     * )
     * @SWG\Tag(name="Utilisateur")
     * @param Utilisateur $utilisateur
     * @param Request $request
     * @param OperationManager $operationManager
     * @return Response
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function postAddAdressWalletAction(
        Utilisateur $utilisateur,
        Request $request,
        OperationManager $operationManager
    ) {
        if ($request->isMethod('POST')) {
            $data = $operationManager->addWallet($utilisateur, $request->request->all());

            return $this->handleView($this->view($data));
        }

        return $this->handleView($this->view(['error' => 'Not found response']));
    }

    /**
     * Route pour ajouter un rib sur cryptizy
     * @Route("/add/rib/{id}", name="addrib", methods={"POST"})
     * @SWG\Response(
     *     response=200,
     *     description="Route pour ajouter un rib sur cryptizy"
     * )
     * @SWG\Parameter(
     *     name="iban",
     *     in="query",
     *     type="string",
     *     description="Return string"
     * )
     * @SWG\Parameter(
     *     name="bic",
     *     in="query",
     *     type="string",
     *     description="Return string"
     * )
     * @SWG\Tag(name="Utilisateur")
     * @param Utilisateur $utilisateur
     * @param OperationManager $operationManager
     * @param Request $request
     * @return Response
     */
    public function postRibAction(
        Utilisateur $utilisateur,
        Request $request,
        OperationManager $operationManager
    ) {
        if ($request->getMethod() == "POST") {
            $data = $operationManager->addRib($utilisateur, $request->request->all());

            return $this->handleView($this->view($data));
        }

        return $this->handleView($this->view(['error' => 'RIB - Not found response']));
    }


    /**
     * Route pour retirer sur votre solde de cryptizy
     * @Route("/retrait/{id}", name="retrait", methods={"POST"})
     * @SWG\Response(
     *     response=200,
     *     description="Route pour retirer sur votre solde de cryptizy"
     * )
     * @SWG\Parameter(
     *     name="solde",
     *     in="query",
     *     type="string",
     *     description="Return string"
     * )
     * @SWG\Tag(name="Utilisateur")
     * @param Utilisateur $utilisateur
     * @param Request $request
     * @param OperationManager $operationManager
     * @return Response
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function postRetraitAction(
        Utilisateur $utilisateur,
        Request $request,
        OperationManager $operationManager
    ) {
        if ($request->getMethod() == "POST" || $request->getMethod() == "post") {
            $data = $operationManager->retrait($utilisateur, $request->request->all());

            return $this->handleView($this->view($data));
        }

        return $this->handleView($this->view(['error' => 'Retrait - Not found response']));
    }

    /**
     * Afficher tout les transferts enregistrés de l'utilisateur
     * @Route("/transfert/{id}", name="transfert", methods={"GET"})
     * @SWG\Response(
     *     response=200,
     *     description="Afficher tout les transferts enregistrés de l'utilisateur"
     * )
     * @SWG\Tag(name="Utilisateur")
     * @param Utilisateur $utilisateur
     * @param RetraitRepository $retraitRepository
     * @return Response
     */
    public function getTransfertUserAction(Utilisateur $utilisateur, RetraitRepository $retraitRepository)
    {
        //Instance Entity Manager
        if (!$utilisateur instanceof Utilisateur) {
            return $this->handleView($this->view(['error' => 'User not found']));
        }

        return $this->handleView($this->view($retraitRepository->getTransfertByUser($utilisateur)));
    }


    /**
     * Afficher tout les transferts enregistrés de l'utilisateur
     * @Route("/affiliation/{id}", name="affiliation", methods={"GET"})
     * @SWG\Response(
     *     response=200,
     *     description="Afficher tout les transferts enregistrés de l'utilisateur"
     * )
     * @SWG\Tag(name="Utilisateur")
     * @param Utilisateur $utilisateur
     * @return Response
     */
    public function getAffiliationUserAction(Utilisateur $utilisateur)
    {
        //Instance Entity Manager
        if (!$utilisateur instanceof Utilisateur) {
            return $this->handleView($this->view(['error' => 'User not found']));
        }
        $parainageRepository = $this->getDoctrine()->getRepository(Parainage::class);
        $data = [
            'parrainage' => $parainageRepository->getStorieParainUser($utilisateur),
            'totalFilleul' => $parainageRepository->getAllFilleulByUser($utilisateur),
            'totalSolde' => $parainageRepository->getAllSoldeParrainageByUser($utilisateur)
        ];

        return $this->handleView($this->view($data));
    }

    /**
     * Afficher tout les depots enregistrés de l'utilisateur
     * @Route("/depot/{id}", name="depot", methods={"GET"})
     * @SWG\Response(
     *     response=200,
     *     description="Afficher tout les depots enregistrés de l'utilisateur"
     * )
     * @SWG\Tag(name="Utilisateur")
     * @param Utilisateur $utilisateur
     * @param DepotManager $depotManager
     * @return Response
     */
    public function getDepotUserAction(Utilisateur $utilisateur, DepotManager $depotManager)
    {
        return $this->handleView($this->view($depotManager->depotByUser($utilisateur)));
    }

    /**
     * Afficher tout les operations enregistrés
     * @Route("/operation/{id}", name="operation", methods={"GET"})
     * @SWG\Response(
     *     response=200,
     *     description="Afficher tout les operations enregistrés"
     * )
     * @SWG\Tag(name="Utilisateur")
     * @param Utilisateur $utilisateur
     * @param OperationManager $operationManager
     * @return Response
     */
    public function getOperationByUserAction(Utilisateur $utilisateur, OperationManager $operationManager)
    {
        return $this->handleView($this->view($operationManager->operationByUser($utilisateur)));
    }

    /**
     * Route pour retirer sur votre solde de cryptizy |  Nouvel ordre de vente
     * @Route("/sellcrypto/{id}", name="sellcrypto", methods={"POST"})
     * @SWG\Response(
     *     response=200,
     *     description="Route pour retirer sur votre solde de cryptizy"
     * )
     * @SWG\Parameter(
     *     name="crypto",
     *     in="query",
     *     type="integer",
     *     description="Return integer , amount crypto to sell"
     * )
     * @SWG\Parameter(
     *     name="target",
     *     in="query",
     *     type="integer",
     *     description="Return integer, choose id crypto"
     * )
     * @SWG\Tag(name="Utilisateur")
     * @param Utilisateur $utilisateur
     * @param Request $request
     * @param OperationManager $operationManager
     * @return Response
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function postSellCryptoAction(
        Utilisateur $utilisateur,
        Request $request,
        OperationManager $operationManager
    ) {
        if ($request->getMethod() == "POST" || $request->getMethod() == "post") {
            $data = $operationManager->sellCrypto($utilisateur, $request->request->all());

            return $this->handleView($this->view($data));
        }

        return $this->handleView($this->view(['SELL CRYPTO - Not found response']));
    }


    /**
     * Route pour transferer vos crypto avec cryptizy
     * @Route("/transfertcrypto/{id}", name="transfertcrypto", methods={"POST"})
     * @SWG\Response(
     *     response=200,
     *     description="Route pour transfert vos crypto avec cryptizy"
     * )
     * @SWG\Parameter(
     *     name="wallet",
     *     in="query",
     *     type="integer",
     *     description="Return integer"
     * )
     * @SWG\Parameter(
     *     name="crypto_id",
     *     in="query",
     *     type="integer",
     *     description="Return integer"
     * )
     * @SWG\Parameter(
     *     name="montant",
     *     in="query",
     *     type="integer",
     *     description="Return integer"
     * )
     * @SWG\Tag(name="Utilisateur")
     * @param Utilisateur $utilisateur
     * @param Request $request
     * @param OperationManager $operationManager
     * @return Response
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function postTransfertCryptoAction(
        Utilisateur $utilisateur,
        Request $request,
        OperationManager $operationManager
    ) {
        if ($request->getMethod() == "POST") {
            $data = $operationManager->transfertCryptoByWallet($utilisateur, $request->request->all());

            return $this->handleView($this->view($data));
        };

        return $this->handleView($this->view(['TRANSFERT CRYPTO - Not found response.']));
    }
}
