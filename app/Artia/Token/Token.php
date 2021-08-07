<?php

namespace App\Artia\Token;

use App\Artia\Api;

class Token
{
    private static $token;
    private static $cache;

    /**
     * Retorna o Endereço do arquivo Token.json
     *
     * @param string $cache [path]
     * @return string
    */
    public static function cache(string $cache = __DIR__ . '/../../../storage/token/token.json'): string
    {
        return self::$cache = $cache;
    }

    /**
     * Retorna o Token
     *
     * @return null|string
    */
    public static function getToken(): ?string
    {
        return self::$token;
    }

    /**
     * Revalida o token de Acesso.
     *
     * @return void
    */
    public static function revalited(): void
    {
        if (Token::loadCache()) {
            $token = Token::loadCache();
            $now = date_create();
            $lasted = date_create($token->date);
            $now->modify('-45 minutes');

            if ($now > $lasted) {
                $api = new Api();
                $api->required([
                    'callback' => 'authenticationByClient'
                ])
                ->build()
                ->send();

                $response = $api->getResponse();

                $fp = fopen(Token::cache(), 'w+');
                fwrite($fp, toJson([
                    'cache' => 'token',
                    'token' => $response->data->authenticationByClient->token,
                    'type'  => 'authenticationByClient',
                    'date'  => dateFormat()->format('Y-m-d H:i:s')
                ]));
                fclose($fp);
            }
        }
    }

    /**
     * Carrega o arquivo Token.json.
     *
     * @return null|object
    */
    public static function loadCache(): ?object
    {
        $file = Token::cache();
        if (!file_exists($file)) {
            return new class {
                public $date = '2020-05-24 22:00:00';
            };
        }

        $content = json_decode(file_get_contents($file));
        self::$token = $content->token;

        return $content;
    }
}
