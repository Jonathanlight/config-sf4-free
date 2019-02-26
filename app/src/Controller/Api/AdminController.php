<?php

namespace App\Controller\Api;

use App\Entity\Utilisateur;
use App\Manager\DepotManager;
use App\Manager\OperationManager;
use App\Manager\UtilisateurManager;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Response;
#Nelmio component
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;

/**
 * @Route("/api/admin_xgt45e8")
 */
class AdminController extends FOSRestController
{
    /**
     * Liste des operations
     * @Route("/dashboard/operations", name="dashboard_operations", methods={"GET"})
     * @SWG\Response(
     *     response=200,
     *     description="Afficher tout les operations enregistrés"
     * )
     * @SWG\Parameter(
     *     name="order",
     *     in="query",
     *     type="string",
     *     description="Retourne la liste des operations"
     * )
     * @SWG\Tag(name="Admin")
     * @param OperationManager $operationManager
     * @return Response
     */
    public function getOperationAction(OperationManager $operationManager)
    {
        return $this->handleView($this->view($operationManager->collection()));
    }

    /**
     * Liste des users
     * @Route("/dashboard/users", name="dashboard_api_users", methods={"GET"})
     * @SWG\Response(
     *     response=200,
     *     description="Afficher tout les utilisateurs enregistrés"
     * )
     * @SWG\Tag(name="Admin")
     * @param UtilisateurManager $utilisateurManager
     * @return Response
     */
    public function getAllUsersAction(UtilisateurManager $utilisateurManager)
    {
        return $this->handleView($this->view($utilisateurManager->collection()));
    }

    /**
     * Liste des depots
     * @Route("/dashboard/depots", name="dashboard_api_depots", methods={"GET"})
     * @SWG\Response(
     *     response=200,
     *     description="Afficher tout les depots enregistrés"
     * )
     * @SWG\Tag(name="Admin")
     * @param DepotManager $depotManager
     * @return Response
     */
    public function getAllDepot(DepotManager $depotManager)
    {
        return $this->handleView($this->view($depotManager->collection()));
    }

    /**
     * Recherche un utilisateur par l'identifient
     * @Route("/dashboard/user/{id}", name="dashboard_api_user", methods={"GET"})
     * @SWG\Response(
     *     response=200,
     *     description="Recherche un utilisateur par l'identifient"
     * )
     * @SWG\Tag(name="Admin")
     * @param Utilisateur $user
     * @return Response
     */
    public function getDashboardUser(Utilisateur $user)
    {
        if (!$user instanceof Utilisateur) {
            return $this->handleView($this->view(['error' => "user not found"]));
        }

        return $this->handleView($this->view($user));
    }
}