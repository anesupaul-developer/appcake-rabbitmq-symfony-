<?php

namespace App\Command;

use App\Definitions\NewsArticle;
use App\Messages\SendNewsArticleInfo;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class NewsParserServiceCommand extends Command
{
    protected static $defaultName = 'app:news';

    private ?string $apiKey;

    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus, KernelInterface $kernel)
    {
        parent::__construct();

        $this->apiKey = $kernel->getContainer()->getParameter('news_api_key');

        $this->messageBus = $messageBus;
    }

    protected function configure(): void
    {
        $this->setDescription('News Parsing Service')
            ->addOption('date', null, InputOption::VALUE_OPTIONAL, 'date');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $date = $input->getOption('date');

        if (empty($date) === false) {
            $io->note(sprintf('You passed a date option: %s', $date));
        }

        $date = empty($date) === false ? date_create($date)->format('Y-m-d') : date('Y-m-d');

        $items = json_decode($this->getArticles($date));
        foreach($items->articles as $article) {
            $definition = new NewsArticle($article->title, $article->description, $article->urlToImage, $article->publishedAt);

            $this->sendToQueue($definition);
        }

        $io->success('Completed');

        return Command::SUCCESS;
    }

    private function getTestArticles()
    {
        return file_get_contents("./tests/_data/news-articles.json");
    }

    private function sendToQueue(NewsArticle $message)
    {
        $this->messageBus->dispatch(new SendNewsArticleInfo($message));
    }

    private function getArticles(string $date, string $search = 'a'): string
    {
        return $this->getTestArticles();

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://newsapi.org/v2/everything?q=".$search."&from=".$date."&sortBy=publishedAt&apiKey=".$this->apiKey,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => "utf-8",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "User-Agent:Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko)
                 Chrome/91.0.4472.114 Safari/537.36"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            throw new $err;
        } else {

            error_log($response, 3, "news_".date_create()->getTimestamp().".txt");
            return $response;
        }
    }
}
