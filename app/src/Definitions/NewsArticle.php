<?php

namespace App\Definitions;

use Ramsey\Uuid\Uuid;
use function trim;
use function preg_replace;
use function str_replace;
use function strtolower;
use function date_create;

class NewsArticle
{
    public string $slug;

    public string $title;

    public ?string $picture_url;

    public string $uuid;

    public string $description;

    public $created_at;

    public function __construct($title, $description, $pictureUrl, $createdAt = null)
    {
        $this->title = $title;

        $this->description = $description;

        $this->picture_url = $pictureUrl;

        if (empty($createdAt) === false) {
            $this->created_at = date_create($createdAt);
        }

        $this->uuid = Uuid::uuid4()->toString();

        $this->slug = $this->generateSlug($title);
    }

    private function generateSlug($title): string
    {
        $slug = preg_replace('/[^a-zA-Z0-9 -]/','',trim(strip_tags(htmlspecialchars($title))));

        return strtolower(str_replace(' ','-', $slug));
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getPictureUrl(): ?string
    {
        return $this->picture_url ?? '';
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->created_at;
    }

}