<?php

use PhpAmqpLib\Connection\AMQPConnectionConfig;
use PhpAmqpLib\Connection\AMQPConnectionFactory;
use PhpAmqpLib\Message\AMQPMessage;

require_once "../vendor/autoload.php";

$dotEnv = Dotenv\Dotenv::createImmutable(__DIR__ . "/..");
$dotEnv->load();

$config = require_once __DIR__ . "/../config.php";

$amqpConfig = new AMQPConnectionConfig();
$amqpConfig->setHost($_ENV['QUEUE_HOST']);
$amqpConfig->setPort($_ENV['QUEUE_PORT']);
$amqpConfig->setUser($_ENV['QUEUE_USER']);
$amqpConfig->setPassword($_ENV['QUEUE_PASSWORD']);

//dd($_ENV['QUEUE_HOST'], $_ENV['QUEUE_PORT'], $_ENV['QUEUE_USER'], $_ENV['QUEUE_PASSWORD']);

$ampqConnection = AMQPConnectionFactory::create($amqpConfig);

$channel = $ampqConnection->channel();

$channel->queue_declare(
    "discord_bot_medias",
    false,
    true,
    false,
    false
);

$callback = function (AMQPMessage $message) {
    echo "[x] Recieved  ", $message->getBody(), "\n";

    $message->ack();
};


$channel->basic_consume(
    "discord_bot_medias",
    "",
    false,
    false,
    false,
    false,
    $callback,
);

/** while there if the server is canceled or the channel is closed is_consuming will be false*/
while ($channel->is_consuming()) {
    $channel->wait();
}

$channel->close();
$ampqConnection->close();