<?php

namespace App\DiscordBot\Music;

use App\DiscordBot\Commands\DownloadCommand;
use YoutubeDl\Options;
use YoutubeDl\YoutubeDl;

class Download
{
    private static Download|null $instance = null;
    private YoutubeDl $youtubeDl;
    private function __construct() {
        $this->youtubeDl = new YoutubeDl();
    }

    public static function getInstance(): Download {
        if(self::$instance === null) {
            return new Download();
        }
        return self::$instance;
    }

    public function downloadMp3(DownloadCommand $downloadCommand) {
        $this->youtubeDl->setBinPath("C:\\yt-dlp\\yt-dlp.exe");
        $this->youtubeDl->onProgress($downloadCommand->getOnProgress());

        echo __DIR__ . "/../../musics";

        $collection = $this->youtubeDl->download(
            Options::create()
                ->downloadPath(__DIR__ . "/../../musics")
                ->extractAudio(true)
                ->audioFormat('mp3')
                ->audioQuality('0')
                ->output("author-{$downloadCommand->getAuthor()->id}-title%(title)s.%(ext)s")
                ->url($downloadCommand->getUrlToDownload())
                ->playlistEnd(1)
        );

        $firstVideo = $collection->getVideos()[0];

        echo $firstVideo->getTitle();
    }
}