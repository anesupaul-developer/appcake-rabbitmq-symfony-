<?php

namespace App\Handler;

use App\Entity\News;
use App\Mappings\NewsArticle as NewsMapper;
use App\Messages\SendNewsArticleInfo;
use App\Repository\NewsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class SendArticleInfoHandler implements MessageHandlerInterface
{
    /**
     * @var MessageBusInterface
     */
    private MessageBusInterface $messageBus;

    private EntityManagerInterface $entityManager;

    public function __construct(MessageBusInterface $messageBus, EntityManagerInterface $entityManager)
    {
        $this->messageBus = $messageBus;

        $this->entityManager = $entityManager;
    }

    public function __invoke(SendNewsArticleInfo $articleInfo)
    {
        try {
            /**
             * @var NewsRepository $newsRepository
             */
            $newsRepository = $this->entityManager->getRepository(News::class);

            /**
             * @var News $exists
             */
            $exists = $newsRepository->findOneBy(['title' => $articleInfo->getArticle()->getTitle()]);
            if (empty($exists) === true) {
                (new NewsMapper($articleInfo->getArticle(), $this->entityManager))->create();
                echo 'Created new article'.PHP_EOL;
            } else {
                $newsRepository->updateNewsArticle($exists, $articleInfo->getArticle());
                echo 'Updated article with id '.$exists->getId().PHP_EOL;
            }
        } catch(\Exception $exception) {
            echo $exception->getMessage().PHP_EOL;
        }
    }
}