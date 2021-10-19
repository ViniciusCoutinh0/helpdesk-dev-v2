<?php

namespace App\Artia\Token;

use App\Artia\Api;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Token
{
    /**
     * @var string
     */
    private static $hash;

    /**
     * @return void
     */
    public static function regenerate()
    {
        if (Token::loadCacheFile()) {
            $logger = new Logger('token');
            $logger->pushHandler(new StreamHandler(__DIR__ . env('CONFIG_PATH_LOG') . '/token.txt', Logger::DEBUG));

            $file = Token::loadCacheFile();

            $now = date_create();
            $last = date_create($file->date);
            $now->modify('-40 minutes');

            if ($now < $last) {
                return null;
            }

            $api = new Api();
            $api->requireds([
                'clientId' => env('CONFIG_API_CLIENT_ID'),
                'secret' => env('CONFIG_API_SECRET')
            ])->authenticationByClient();

            $response = $api->response();

            if (isset($response->errors[0])) {
                $logger->warning($response->errors[0]->message);
            }

            $fopen = fopen(__DIR__ . env('CONFIG_API_TOKEN_CACHE'), 'w+');
            fwrite($fopen, json_encode([
                'cache' => 'token',
                'token' => $response->data->authenticationByClient->token,
                'type'  => 'authenticationByClient',
                'date'  => date('Y-m-d H:i:s')
            ]));
            fclose($fopen);
        }
    }

    /**
     * @return null|string
     */
    public static function hash(): ?string
    {
        return self::$hash;
    }

    /**
     * @return object
     */
    public static function loadCacheFile(): object
    {
        $file = __DIR__ . env('CONFIG_API_TOKEN_CACHE');

        if (!file_exists($file)) {
            return new class
            {
                public $date = '2021-01-01 00:00:00';
            };
        }

        $content = json_decode(file_get_contents($file));
        self::$hash = $content->token;

        return $content;
    }
}
