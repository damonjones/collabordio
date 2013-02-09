<?php

namespace HT\AppBundle\Service;

class Rdio
{
    protected $client;
    protected $endpoint;
    protected $requestTokenUrl;
    protected $accessTokenUrl;

    public function __construct(GuzzleOAuthClient $client, $endpoint, $requestTokenUrl, $accessTokenUrl)
    {
        $this->client = $client;
        $this->endpoint = $endpoint;
        $this->requestTokenUrl = $requestTokenUrl;
        $this->accessTokenUrl = $accessTokenUrl;
    }

    public function requestToken($callbackUrl)
    {
        $response = $this->client->sendRequest($this->requestTokenUrl, array('oauth_callback' => $callbackUrl));
        $this->saveTokens($response);

        return $response['login_url'] . '?oauth_token=' . $response['oauth_token'];
    }

    public function accessToken($oauthVerifier)
    {
        $response = $this->client->sendRequest($this->accessTokenUrl, array('oauth_verifier' => $oauthVerifier));
        $this->saveTokens($response);
    }

    public function saveTokens(array $arr)
    {
        $this->client->setToken($arr['oauth_token'], $arr['oauth_token_secret']);
    }

    public function sendRequest($method, $parameters = array())
    {
        $parameters['method'] = $method;

        return $this->client->sendRequest($this->endpoint, $parameters);
    }

    public function getToken()
    {
        return $this->client->getToken();
    }

    public function getTokenSecret()
    {
        return $this->client->getTokenSecret();
    }
}