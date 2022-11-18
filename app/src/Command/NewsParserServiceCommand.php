<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class NewsParserServiceCommand extends Command
{
    protected static $defaultName = 'api:news';

    protected function configure(): void
    {
        $this->setDescription('News Parsing Service')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        $this->getArticles();

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            // ...
        }

        $io->success('Completed');

        return Command::SUCCESS;
    }

    private function getArticles(): void
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://bing-news-search1.p.rapidapi.com/news?safeSearch=Off&textFormat=Raw",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "X-BingApis-SDK: true",
                "X-RapidAPI-Host: bing-news-search1.p.rapidapi.com",
                "X-RapidAPI-Key: 47da856005mshf3fae3b93f96d6ap135f10jsna44ae360f523"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            error_log($response, 3, 'news.json');
        }
    }
}
