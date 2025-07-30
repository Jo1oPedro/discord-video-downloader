<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use YoutubeDl\Options;
use YoutubeDl\YoutubeDl;

$yt = new YoutubeDl();

$yt->setBinPath("C:\\yt-dlp\\yt-dlp.exe");

$collection = $yt->download(
    Options::create()
        ->downloadPath(__DIR__ . "/../../musics")
        /*->extractAudio(true)
        ->audioFormat('mp4')
        ->audioQuality('0')*/
        ->output('%(title)s.%(ext)s')
        ->url('https://www.youtube.com/watch?v=A7ry4cx6HfY&list=RDA7ry4cx6HfY')
        ->playlistEnd(1)
);

$firstVideo = $collection->getVideos()[0];

echo $firstVideo->getTitle();

/*foreach ($collection->getVideos() as $video) {
    if ($video->getError() !== null) {
        echo "Error downloading video: {$video->getError()}.";
    } else {
        echo $video->getTitle();
    }
}*/