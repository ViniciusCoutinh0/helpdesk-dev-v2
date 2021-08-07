<?php

namespace App\Artia\Request;

use App\Artia\Token\Token;

class Request extends Token
{
    /**
     * Envia uma requisição via CURL
     *
     * @param string $graphQl
     * @param null|string $token
     */
    protected function curl(string $graphQl, string $token = null)
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $_ENV['ARTIA_URL_BASE'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POSTFIELDS => json_encode(['query' => $graphQl]),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'OrganizationId: ' . $_ENV['ARTIA_ORGANIZATION_ID'],
                'Authorization: ' . Token::getToken()
            ]
        ]);

        $response = json_decode(curl_exec($ch));

        if (isset($response->errors)) {
            throw new \Exception($response->errors[0]->message);
        }

        return $response;
    }
}
