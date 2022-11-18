<?php

namespace App\Command;

use App\Definitions\NewsArticle;
use App\Mappings\NewsArticle as NewsMapper;
use App\Entity\News;
use App\Repository\NewsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function file_get_contents;
use function count;

class SeedNewsCommand extends Command
{
    protected static $defaultName = 'seed:news';

    private EntityManagerInterface $entityManager;

    private array $articles = [];

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        $articles = json_decode(file_get_contents("./tests/_data/news-articles.json"));

        $this->articles = $articles->articles;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Seed test news records');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        /**
         * @var NewsRepository $newsRepository
         */
        $newsRepository = $this->entityManager->getRepository(News::class);

        foreach ($this->articles as $article) {
            $definition = new NewsArticle($article->title, $article->description, $article->urlToImage, $article->publishedAt);

            /**
             * @var News $exists
             */
            $exists = $newsRepository->findOneBy(['title' => $article->title]);
            if (empty($exists) === true) {
                (new NewsMapper($definition, $this->entityManager))->create();
            } else {
                $this->updateNewsArticle($exists, $definition);
            }
        }

        $io->success('Added: ' . count($this->articles) . ' new articles' . PHP_EOL);

        return Command::SUCCESS;
    }

    private function updateNewsArticle(News $article, NewsArticle $definition): void
    {
        $article
            ->setLastUpdatedAt(date_create())
            ->setTitle($definition->getTitle())
            ->setSlug($definition->getSlug())
            ->setDescription($definition->getDescription())
            ->setPictureUrl($definition->getPictureUrl());

        $this->entityManager->persist($article);
        $this->entityManager->flush();
    }
}
