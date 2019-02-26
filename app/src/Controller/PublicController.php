<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\CryptoRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PublicController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     * @Route("/home/{_locale}", name="home", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     * @param CryptoRepository $cryptoRepository
     * @param ArticleRepository $articleRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function home(CryptoRepository $cryptoRepository, ArticleRepository $articleRepository): Response
    {
        return $this->render('public/index.html.twig', [
            'cryptos' => $cryptoRepository->findAll(),
            'articles' => $articleRepository->findAllArticleActive(),
        ]);
    }

    /**
     * @Route("/cgu/{_locale}", name="cgu", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     * @return Response
     */
    public function cgu(): Response
    {
        return $this->render('public/cgu.html.twig');
    }

    /**
     * @Route("/privacy/{_locale}", name="privacy", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     * @return Response
     */
    public function privacy(): Response
    {
        return $this->render('public/privacy.html.twig');
    }

    /**
     * @Route("/aml/{_locale}", name="aml", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     * @return Response
     */
    public function aml(): Response
    {
        return $this->render('public/aml.html.twig');
    }


    /**
     * @Route("/a-propos/{_locale}", name="aPropos", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     * @return Response
     */
    public function aPropos(): Response
    {
        return $this->render('/public/a-propos.html.twig');
    }
}
