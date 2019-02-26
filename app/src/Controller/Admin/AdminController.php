<?php

namespace App\Controller\Admin;

use App\Entity\Transfertcrypto;
use App\Manager\AdminManager;
use App\Manager\TransfertCryptoManager;
use App\Repository\ArticleRepository;
use App\Repository\CategorieRepository;
use App\Repository\DepotRepository;
use App\Repository\ParainageRepository;
use App\Repository\SoldeRepository;
use App\Manager\SellManager;
use App\Repository\RetraitRepository;
use App\Repository\UtilisateurRepository;
use App\Repository\VendreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Article;
use App\Entity\Utilisateur;
use App\Repository\OperationRepository;
use Symfony\Component\Security\Core\User\UserInterface;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin_xgt45e8/", name="dashboard", methods={"GET","POST"})
     * @param Request $request
     * @param AdminManager $adminManager
     * @param OperationRepository $operationRepository
     * @param SoldeRepository $soldeRepository
     * @param UtilisateurRepository $utilisateurRepository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function dashboard(
        Request $request,
        AdminManager $adminManager,
        OperationRepository $operationRepository,
        SoldeRepository $soldeRepository,
        UtilisateurRepository $utilisateurRepository
    ) {
        $transactions = $operationRepository->getAllOperationOnline();
        $soldeGerer = $soldeRepository->getCostSumAllTransaction();
        $chiffreAffaire = (0.04 * $soldeGerer['soldeAll']);
        $totalUser = $utilisateurRepository->getSumAllUser();
        $soldeDispoRepo = $soldeRepository->soldeDisponible();
        $soldeDispo = $soldeDispoRepo['soldeDispo'];

        if ($request->isMethod('POST')) {
            $data = $request->request->all();
            $adminManager->operation($data);
            return $this->redirectToRoute('dashboard');
        }
        return $this->render('admin/index.html.twig', [
            'transactions' => $transactions,
            'soldeDispo' => $soldeDispo,
            'soldeGerer' => $soldeGerer['soldeAll'],
            'chiffreAffaire' => $chiffreAffaire,
            'totalUser' => $totalUser
        ]);
    }

    /**
     * @Route("/admin_xgt45e8/list/depots", name="list_depot", methods={"GET","POST"})
     * @param DepotRepository $depotRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function depots(DepotRepository $depotRepository)
    {
        return $this->render('admin/depots.html.twig', [
            'allDepos' => $depotRepository->findAll()
        ]);
    }

    /**
     * @Route("/admin_xgt45e8/list/depot/online", name="list_depot_online", methods={"GET","POST"})
     * @param Request $request
     * @param DepotRepository $depotRepository
     * @param AdminManager $adminManager
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function allDepotOnline(
        Request $request,
        DepotRepository $depotRepository,
        AdminManager $adminManager
    ) {
        if ($request->isMethod('POST')) {
            $adminManager->confirmerDepot($request->request->all());
        }

        return $this->render('admin/depot.html.twig', [
            'allDepos' => $depotRepository->getDepotByCarteBancaire()
        ]);
    }

    /**
     * @Route("/admin_xgt45e8/list/depot/disable", name="list_depot_disable", methods={"GET","POST"})
     * @param Request $request
     * @param AdminManager $adminManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function allDepotDisable(Request $request, AdminManager $adminManager)
    {
        if ($request->isMethod('POST')) {
            $adminManager->disableDepot($request->request->all());
            return $this->redirectToRoute('list_depot_online');
        }
    }

    /**
     * @Route("/admin_xgt45e8/list/users", name="list_user", methods={"GET","POST"})
     * @param AdminManager $adminManager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function users(AdminManager $adminManager)
    {
        return $this->render('admin/user.html.twig', [
            'users' => $adminManager->listUsers()
        ]);
    }

    /**
     * @Route("/admin_xgt45e8/details_user/{id}", name="details_user", methods={"GET","POST"})
     * @param Utilisateur $utilisateur
     * @param Request $request
     * @param AdminManager $adminManager
     * @param OperationRepository $operationRepository
     * @param DepotRepository $depotRepository
     * @param ParainageRepository $parainageRepository
     * @param SellManager $sellManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function user_detailsAction(
        Utilisateur $utilisateur,
        Request $request,
        AdminManager $adminManager,
        OperationRepository $operationRepository,
        DepotRepository $depotRepository,
        ParainageRepository $parainageRepository,
        SellManager $sellManager
    ) {
        if ($request->isMethod('POST')) {
            $adminManager->newDepot($utilisateur, $request->request->all());
            return $this->redirectToRoute('details_user', ['id' => $utilisateur->getId()]);
        }

        return $this->render('admin/user_details.html.twig', [
            'user'=> $utilisateur,
            'depotUser' => $depotRepository->getDepotValideByUser($utilisateur),
            'depotUserEnCour' => $depotRepository->getDepotEnCoursByUser($utilisateur),
            'totalDepose' => round($utilisateur->getSolde()->getSoldeOld() - $sellManager->getAllSumVirementByUser($utilisateur), 2),
            'transactions' => $operationRepository->getOperationByUser($utilisateur),
            'monParrain' => $parainageRepository->getStorieFilleulUser($utilisateur),
            'listFilleuls' => $parainageRepository->getStorieParainUser($utilisateur)
        ]);
    }

    /**
     * @Route("/admin_xgt45e8/validated/user/{id}", name="validated_user", methods={"GET","POST"})
     * @param Utilisateur $utilisateur
     * @param Request $request
     * @param AdminManager $adminManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function validatedUser(
        Utilisateur $utilisateur,
        Request $request,
        AdminManager $adminManager
    ) {
        if ($request->isMethod('GET')) {
            $adminManager->validated_user($utilisateur);
        }

        return $this->redirectToRoute('details_user', ['id' => $utilisateur->getId()]);
    }

    /**
     * @Route("/admin_xgt45e8/disable_user/{id}", name="disable_user", methods={"GET","POST"})
     * @param Utilisateur $utilisateur
     * @param Request $request
     * @param AdminManager $adminManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function disable_userAction(
        Utilisateur $utilisateur,
        Request $request,
        AdminManager $adminManager
    ) {
        if ($request->isMethod('GET')) {
            $adminManager->disable_user($utilisateur);
        }

        return $this->redirectToRoute('details_user', ['id' => $utilisateur->getId()]);
    }

    /**
     * @Route("/admin_xgt45e8/pending", name="pending", methods={"GET","POST"})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function pending()
    {
        return $this->render('admin/pending.html.twig');
    }

    /**
     * @Route("/admin_xgt45e8/history", name="history", methods={"GET","POST"})
     * @param OperationRepository $operationRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function history(OperationRepository $operationRepository)
    {
        return $this->render('admin/history.html.twig', [
            'transactions' => $operationRepository->getAllOperationSuccess()
        ]);
    }

    /**
     * @Route("/admin_xgt45e8/history/sell", name="history_sell", methods={"GET","POST"})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function historySell(VendreRepository $vendreRepository)
    {
        return $this->render('admin/history_sell.html.twig', [
            'historiqueVentes' => $vendreRepository->getVentes()
        ]);
    }

    /**
     * @Route("/admin_xgt45e8/sales", name="sales", methods={"GET","POST"})
     * @param Request $request
     * @param VendreRepository $vendreRepository
     * @param AdminManager $adminManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sales(
        Request $request,
        VendreRepository $vendreRepository,
        AdminManager $adminManager
    ) {
        if ($request->isMethod('POST')) {
            $adminManager->sales($request->request->all());
            return $this->redirectToRoute('sales');
        }

        return $this->render('admin/sales.html.twig', [
            "vendres" => $vendreRepository->getAllVenteOnline()
        ]);
    }

    /**
     * @Route("/admin_xgt45e8/transfert/crypto", name="transfertAdmin", methods={"GET","POST"})
     * @param Request $request
     * @param AdminManager $adminManager
     * @param TransfertCryptoManager $transfertCryptoManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function retrait(
        Request $request,
        AdminManager $adminManager,
        TransfertCryptoManager $transfertCryptoManager
    ) {
        if ($request->isMethod('POST')) {
            $adminManager->transfert($request->request->all());

            return $this->redirectToRoute('transfertAdmin');
        }

        return $this->render('admin/transfertAdmin.html.twig', [
            'allTransferts' => $transfertCryptoManager->collection()
        ]);
    }

    /**
     * @Route("/admin_xgt45e8/transfert/delete/{id}", name="transfert_delete")
     * @param Transfertcrypto $transfertcrypto
     * @param TransfertCryptoManager $transfertCryptoManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(
        Transfertcrypto $transfertcrypto,
        TransfertCryptoManager $transfertCryptoManager
    ) {
        $transfertCryptoManager->delete($transfertcrypto);

        return $this->redirectToRoute('transfertAdmin');
    }

    /**
     * @Route("/admin_xgt45e8/retrait", name="retraitAdmin", methods={"GET","POST"})
     * @param Request $request
     * @param AdminManager $adminManager
     * @param RetraitRepository $retraitRepository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function retraits(
        Request $request,
        AdminManager $adminManager,
        RetraitRepository $retraitRepository
    ) {
        if ($request->isMethod('POST')) {
            $adminManager->retrait($request->request->all());
            return $this->redirectToRoute('retraitAdmin');
        }

        return $this->render('admin/retraitAdmin.html.twig', [
            'retraits' => $retraitRepository->findAll()
        ]);
    }

    /**
     * @Route("/admin_xgt45e8/reloadmail/{token}", name="reloadmail", methods={"GET","POST"})
     * @param $token
     * @param AdminManager $adminManager
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function reloadmail($token, AdminManager $adminManager, Request $request)
    {
        $adminManager->reloadmail($token, $request->getLocale());

        return $this->redirectToRoute('list_user');
    }

    /**
     * @Route("/admin_xgt45e8/reloadmailverification/{token}", name="reloadmailverification", methods={"GET","POST"})
     * @param $token
     * @param AdminManager $adminManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function reloadmailverification($token, AdminManager $adminManager)
    {
        $adminManager->reloadmailverification($token);

        return $this->redirectToRoute('list_user');
    }

    /**
     * @Route("/admin_xgt45e8/adminBlog", name="adminBlog", methods={"GET","POST"})
     * @param Request $request
     * @param AdminManager $adminManager
     * @param ArticleRepository $articleRepository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function adminBlog(
        Request $request,
        AdminManager $adminManager,
        ArticleRepository $articleRepository
    ) {
        if ($request->isMethod('POST')) {
            $data = $request->request->all();

            if ($data['categorie']) {
                $adminManager->newCategorie($data);
                return $this->redirectToRoute('adminBlog');
            }
        }

        return $this->render('/admin/adminBlog.html.twig', [
            'allArticles' => $articleRepository->findAll()
        ]);
    }

    /**
     * @Route("/admin_xgt45e8/adminArticleBlog", name="adminArticleBlog", methods={"GET","POST"})
     * @param Request $request
     * @param AdminManager $adminManager
     * @param CategorieRepository $categorieRepository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function adminArticleBlog(
        Request $request,
        AdminManager $adminManager,
        CategorieRepository $categorieRepository
    ) {
        if ($request->isMethod('POST')) {
            $adminManager->newArticle($this->getUser(), $request->request->all());
            return $this->redirectToRoute('adminBlog');
        }

        return $this->render('/admin/adminArticleBlog.html.twig', [
            'allCategories' => $categorieRepository->findAll()
        ]);
    }

    /**
     * @Route("/admin_xgt45e8/article/edit/{id}", name="adminEditArticle", methods={"GET","POST"})
     * @param Article $article
     * @param Request $request
     * @param AdminManager $adminManager
     * @param CategorieRepository $categorieRepository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editArticle(
        Article $article,
        Request $request,
        AdminManager $adminManager,
        CategorieRepository $categorieRepository
    ) {
        if ($request->isMethod('POST')) {
            $data = $request->request->all();
            $adminManager->editArticle($article, $data);
            return $this->redirectToRoute('adminEditArticle', ['id'=>$data['id']]);
        }

        return $this->render('/admin/adminEditArticle.html.twig', [
            'allCategories' => $categorieRepository->findAll(),
            'article' => $article
        ]);
    }

    /**
     * @Route("/admin_xgt45e8/article/delete/{id}", name="adminDeleteArticle", methods={"GET","POST"})
     * @param Article $article
     * @param Request $request
     * @param AdminManager $adminManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteArticle(
        Article $article,
        Request $request,
        AdminManager $adminManager
    ) {
        if ($request->isMethod('GET')) {
            $adminManager->deleteArticle($article);
        }

        return $this->redirectToRoute('adminBlog');
    }

    /**
     * @Route("/admin_xgt45e8/article/active/{id}", name="adminActiveArticle", methods={"GET","POST"})
     * @param Article $article
     * @param Request $request
     * @param AdminManager $adminManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function adminActiveArticle(
        Article $article,
        Request $request,
        AdminManager $adminManager
    ) {
        if ($request->isMethod('GET')) {
            $adminManager->activeArticle($article);
            return $this->redirectToRoute('adminBlog');
        }

        return $this->redirectToRoute('adminBlog');
    }
}
