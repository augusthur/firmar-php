<?php

namespace Augusthur\FirmarIntegration;

class FirmarService
{
    protected $urlRA;
    protected $urlSigner;
    protected $apiKey;
    protected $apiSecret;
    protected $defaultRedirect;
    protected $tokenStorage;
    protected $client;

    public function __construct($options, $tokenStorage, $client = null)
    {
        $options = array_merge([
            'urlRA' => 'https://firmar.gob.ar/RA',
            'urlSigner' => 'https://firmar.gob.ar/firmador',
            'apiKey' => null,
            'apiSecret' => null,
            'defaultRedirect' => null,
        ], $options);
        $this->urlRA = $options['urlRA'];
        $this->urlSigner = $options['urlSigner'];
        $this->apiKey = $options['apiKey'];
        $this->apiSecret = $options['apiSecret'];
        $this->defaultRedirect = $options['defaultRedirect'];
        $this->tokenStorage = $tokenStorage;
        if (isset($client)) {
            $this->client = $client;
        } else {
            $this->client = new \GuzzleHttp\Client();
        }
    }

    public function handleCallback($body)
    {
        $file = tmpfile();
        fwrite($file, base64_decode($base64_string));
        fclose($file);
    }

    public function getAccessToken()
    {
        $access = $this->tokenStorage->get();
        if ($access['expired']) {
            $firmarResponse = $this->client->request('POST', $this->urlRA.'/oauth/token', [
                'auth' => [$this->apiKey, $this->apiSecret],
                'query' => [
                    'grant_type' => 'client_credentials',
                ],
                'headers' => [
                    'User-Agent' => 'signar',
                    'Accept'     => 'application/json',
                ],
            ]);
            $data = json_decode($firmarResponse->getBody(), true);
            $params = [
                'token' => $data['access_token'],
                'expires_at' => time() + $data['expires_in'],
            ];
            $this->tokenStorage->set($params);
            $token = $data['access_token'];
        } else {
            $token = $access['token'];
        }
        return $token;
    }

    public function createSignRequest($cuil, $doc, $type = 'PDF', $data = [], $redirect = null)
    {
        $token = $this->getAccessToken();
        $body = [
            'cuil' => $cuil,
            'type' => $type,
            'documento' => $doc,
            'metadata' => $data,
        ];
        if (isset($redirect)) {
            $body['urlRedirect'] = $redirect;
        }
        $firmarResponse = $this->client->request('POST', $this->urlSigner.'/api/signatures', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'User-Agent' => 'signar',
            ],
            'json' => $body,
        ]);
        $url = $firmarResponse->getHeader('Location');
        if (count($url) > 0) {
            return $url[0];
        } else {
            return null;
        }
    }
}
