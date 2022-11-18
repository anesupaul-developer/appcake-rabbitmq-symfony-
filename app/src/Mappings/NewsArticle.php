<?php

namespace App\Mappings;

use App\Definitions\NewsArticle as Definition;
use App\Entity\News;
use Doctrine\ORM\EntityManagerInterface;

class NewsArticle
{
    private Definition $article;

    private EntityManagerInterface $entityManager;

    public function __construct(Definition $article, EntityManagerInterface $entityManager)
    {
        $this->article = $article;

        $this->entityManager = $entityManager;
    }

    public function create()
    {
        $newsArticle = new News();

        $newsArticle
            ->setUuid($this->article->getUuid())
            ->setTitle($this->article->getTitle())
            ->setSlug($this->article->getSlug())
            ->setDescription($this->article->getDescription())
            ->setPictureUrl($this->article->getPictureUrl())
            ->setCreatedAt($this->article->getCreatedAt());

        $this->entityManager->persist($newsArticle);

        $this->entityManager->flush();
    }
}