<?php

namespace App\Artia\Request;

class Request
{
    /**
     * @var string
    */
    protected $url;

    /**
     * @var array
    */
    protected $headers;

    /**
     * @var string
    */
    protected $method;

    /**
     * @var string
    */
    protected $graphQl;

    protected function curl()
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $this->method,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POSTFIELDS => json_encode(['query' => $this->graphQl]),
            CURLOPT_HTTPHEADER => $this->headers
        ]);

        $response = curl_exec($ch);

        return $response;
    }
}
