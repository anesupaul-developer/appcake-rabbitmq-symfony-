<?php

namespace App\Controller;

use App\Entity\News;
use App\Repository\NewsRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class NewsController extends AbstractController
{
    public function index(NewsRepository $newsRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $newsRepository->getQueryBuilder();
        $pagination   = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            $newsRepository->itemsPerPage
        );

        return $this->render('news/index.html.twig', ['pagination' => $pagination]);
    }

    public function show(News $article): Response
    {
        return $this->render('news/show.html.twig', ['article' => $article]);
    }

    public function destroy(News $article, NewsRepository $newsRepository): Response
    {
        $newsRepository->remove($article, true);

        return $this->redirect($this->generateUrl('news_index'));
    }
}
