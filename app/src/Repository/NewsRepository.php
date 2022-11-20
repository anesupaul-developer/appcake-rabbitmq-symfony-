<?php

namespace App\Repository;

use App\Definitions\NewsArticle;
use App\Entity\News;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<News>
 *
 * @method News|null find($id, $lockMode = null, $lockVersion = null)
 * @method News|null findOneBy(array $criteria, array $orderBy = null)
 * @method News[]    findAll()
 * @method News[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NewsRepository extends ServiceEntityRepository
{
    public int $itemsPerPage = 10;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, News::class);
    }

    public function add(News $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(News $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param string|null $term
     * @return QueryBuilder
     */
    public function getQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('c')->orderBy('c.created_at', 'DESC');
    }

    public function updateNewsArticle(News $article, NewsArticle $definition): void
    {
        $article
            ->setLastUpdatedAt(date_create())
            ->setTitle($definition->getTitle())
            ->setSlug($definition->getSlug())
            ->setDescription($definition->getDescription())
            ->setPictureUrl($definition->getPictureUrl());

        $this->getEntityManager()->persist($article);
        $this->getEntityManager()->flush();
    }
}
