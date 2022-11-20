<?php

namespace App\Messages;

use App\Definitions\NewsArticle;

class SendNewsArticleInfo
{
    /**
     * @var NewsArticle
     */
    private NewsArticle $article;

    public function __construct(NewsArticle $article)
    {
        $this->article = $article;
    }

    /**
     * @return NewsArticle
     */
    public function getArticle(): NewsArticle
    {
        return $this->article;
    }


}