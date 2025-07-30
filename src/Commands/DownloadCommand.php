<?php

namespace App\DiscordBot\Commands;

use Discord\Parts\User\User;

class DownloadCommand implements Command
{
    public function __construct(
        private User $author,
        private string $urlToDownload,
        private $onProgress,
    ) {}
    
    public function getAuthor(): User
    {
        return $this->author;
    }
    
    public function getUrlToDownload(): string
    {
        return $this->urlToDownload;
    }

    public function getOnProgress(): callable
    {
        return $this->onProgress;
    }
}