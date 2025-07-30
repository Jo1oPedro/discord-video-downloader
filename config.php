<?php

return [
    "aws" => [
        'version' => 'latest',
        'region' => $_ENV['AWS_REGION'],
        'credentials' => [
            'key' => $_ENV['AWS_KEY'],
            'secret' => $_ENV['AWS_SECRET'],
        ]
    ]
];