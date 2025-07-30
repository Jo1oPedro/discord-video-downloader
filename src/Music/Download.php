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

        $collection = $this->youtubeDl->download(
            Options::create()
                ->downloadPath(__DIR__ . "/../../musics")
                ->extractAudio(true)
                ->audioFormat('mp3')
                ->audioQuality('0')
                ->output("author-{$downloadCommand->getAuthor()->id}-title%(title)s.%(ext)s-mp3")
                ->url($downloadCommand->getUrlToDownload())
                ->playlistEnd(1)
        );

        $firstVideo = $collection->getVideos()[0];

        $sizeBytes = filesize($firstVideo->getFileName());

        return [
            "path" => $firstVideo->getFileName(),
            "size" => $sizeBytes,
            "name" => $firstVideo->getFilename()
        ];
    }

    public function downloadMp4(DownloadCommand $downloadCommand) {
        $this->youtubeDl->setBinPath("C:\\yt-dlp\\yt-dlp.exe");
        $this->youtubeDl->onProgress($downloadCommand->getOnProgress());

        $collection = $this->youtubeDl->download(
            Options::create()
                ->downloadPath(__DIR__ . "/../../musics")
                ->output("author-{$downloadCommand->getAuthor()->id}-title%(title)s.%(ext)s-mp4")
                ->url($downloadCommand->getUrlToDownload())
                ->playlistEnd(1)
        );

        $firstVideo = $collection->getVideos()[0];

        $sizeBytes = filesize($firstVideo->getFileName());

        return [
            "path" => $firstVideo->getFileName(),
            "size" => $sizeBytes,
            "name" => $firstVideo->getFilename()
        ];
    }
}