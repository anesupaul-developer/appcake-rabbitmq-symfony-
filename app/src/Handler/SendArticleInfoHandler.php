<?php

namespace App\Handler;

use App\Messages\SendNewsArticleInfo;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class SendArticleInfoHandler implements MessageHandlerInterface
{
    /**
     * @var MessageBusInterface
     */
    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function __invoke(SendNewsArticleInfo $articleInfo)
    {
        sleep(5);
        error_log($articleInfo->getArticle()->getTitle(), 3, "article_".date_create()->getTimestamp().".txt");
        echo 'Ok'.PHP_EOL;
    }
}