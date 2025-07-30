<?php

use App\DiscordBot\Commands\DownloadCommand;
use App\DiscordBot\Music\Download;
use Discord\Builders\Components\Option;
use Discord\Builders\Components\StringSelect;
use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\Parts\Interactions\Interaction;
use Discord\WebSockets\Event;
use Discord\WebSockets\Intents;

require_once "vendor/autoload.php";

$dotEnv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotEnv->load();

$discord = new Discord([
    'token' => $_ENV['DISCORD_TOKEN'],
    'intents' => Intents::getDefaultIntents() | Intents::MESSAGE_CONTENT
]);

$youtubeDl = Download::getInstance();

$pendingDownloads = [];

$discord->on('ready', function (Discord $discord) use ($youtubeDl, &$pendingDownloads) {
    $discord->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) use ($youtubeDl, &$pendingDownloads) {
        echo $message->author->id;
        $onProgress = function (?string $target, string $percentage, ?string $size, ?string $speed, ?string $eta, ?string $totalTime) use ($message) {
            $text = "📥 Baixando: $percentage ($size)";
            $message->channel->sendMessage($text);
        };

        if(str_contains($message->content, "!download")) {
            $url = trim(str_replace("!download", "", $message->content));

            $downloadCommand = new DownloadCommand(
                $message->author,
                $url,
                $onProgress
            );

            $origMsgId = $message->id;
            $pendingDownloads[$origMsgId] = $downloadCommand;

            $select = StringSelect::new()
                ->setCustomId("download_format:{$origMsgId}")
                ->setPlaceholder('Escolha o formato…')
                ->addOption(Option::new('MP3', 'mp3')
                ->setDescription('Só o áudio'))
                ->addOption(Option::new('MP4', 'mp4')
                    ->setDescription('Áudio + vídeo'));

            $messageBuilder = MessageBuilder::new();
            $messageBuilder
                ->setContent("Você pediu para baixar:\n{$url}")
                ->addComponent($select);

            $message->channel->sendMessage($messageBuilder);
        }
    });
});

$discord->on(Event::INTERACTION_CREATE, function (Interaction $interaction) use ($youtubeDl, &$pendingDownloads) {
    $cid = $interaction->data->custom_id;

    if(!str_starts_with($cid, "download_format:")) {
        return;
    }

    [, $origMsgId] = explode(":", $cid, 2);

    $downloadCommand = $pendingDownloads[$origMsgId] ?? null;

    if(!$downloadCommand) {
        return $interaction->respondWithMessage("❌ Não encontrei a URL (talvez tenha expirado?).");
    }

    $format = $interaction->data->values[0];

    $interaction->acknowledge()->then(function () use ($interaction, $youtubeDl, $downloadCommand, $format) {
        if($format === "mp3") {
            $youtubeDl->downloadMp3($downloadCommand);
        }

        $interaction->sendFollowUpMessage(
            MessageBuilder::new()
            ->setContent("Download concluído"),
            true
        );
    });

    unset($pendingDownloads[$origMsgId]);
});

$discord->run();