<?php

namespace App\Controller;

use App\Definitions\NewsArticle;
use App\Messages\SendNewsArticleInfo;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

class SendArticleController extends AbstractController
{
    private ?string $apiKey;

    private ?string $articleDate;

    public function __construct()
    {
        $this->apiKey = $this->getParameter('news_api_key');

        $this->articleDate = date('Y-m-d');
    }

    public function sendArticle(MessageBusInterface $messageBus): Response
    {
        try {
            $articles = $this->getArticles();

            $articles = json_encode($articles);
            foreach($articles as $article) {
                $definition = new NewsArticle($article->title, $article->description, $article->urlToImage, $article->publishedAt);
                $messageBus->dispatch(new SendNewsArticleInfo($definition));
            }

            return new Response('Message sent successfully');
        } catch (\Exception $exception) {
            return new Response($exception->getMessage(), 500);
        }

    }

    private function getArticles(string $search = ''): string
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://newsapi.org/v2/everything?q=".$search."&from=".$this->articleDate."&sortBy=publishedAt&apiKey=".$this->apiKey,
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
            return $response;
        }
    }
}