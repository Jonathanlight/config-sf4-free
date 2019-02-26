<?php
/**
 * Created by PhpStorm.
 * User: Jonathan
 * Date: 08/10/2018
 * Time: 00:12
 */

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Manager\ArticleManager;
use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @Route("/blog/{_locale}", name="blog", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     * @param ArticleRepository $articleRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function blog(ArticleRepository $articleRepository)
    {
        return $this->render('/public/blog.html.twig', [
            'articles' => $articleRepository->findAllArticleActive(),
            'lastArticle' => $articleRepository->findLast(),
        ]);
    }

    /**
     * @Route("/articleBlog/{id}/{slug}/{_locale}", name="articleBlog", methods={"GET","POST"}, defaults={"_locale" = "fr", "slug" = null})
     * @param Article $article
     * @param Request $request
     * @param ArticleManager $articleManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function articleBlog(Article $article, Request $request, ArticleManager $articleManager)
    {
        $user = $this->getUser();
        if ($request->isMethod('POST')) {
            if (!$user instanceof Utilisateur) {
                return $this->redirectToRoute('blog');
            }
            $articleManager->newComment($article, $user, $request->request->all());
        }

        return $this->render('/public/articleBlog.html.twig', [
            'article' => $article
        ]);
    }
}