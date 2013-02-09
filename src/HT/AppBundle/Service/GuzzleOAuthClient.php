<?php

namespace HT\AppBundle\Service;

use Guzzle\Http\Client;
use Guzzle\Plugin\Oauth\OauthPlugin;
use Guzzle\Http\Exception\ClientErrorResponseException;
use Guzzle\Http\Exception\ServerErrorResponseException;
use Guzzle\Common\Exception\RuntimeException;

use HT\AppBundle\Service\Exception\NotAuthorisedException;
use HT\AppBundle\Service\Exception\ServerErrorException;

class GuzzleOAuthClient
{
    protected $consumerKey;
    protected $consumerSecret;
    protected $token;
    protected $tokenSecret;
    protected $baseUrl;

    /** @var \Guzzle\Http\Client */
    protected $httpClient;
    /** @var \Guzzle\Plugin\Oauth\OauthPlugin */
    protected $oauthPlugin;

    public function __construct($consumerKey, $consumerSecret, $baseUrl)
    {
        $this->consumerKey = $consumerKey;
        $this->consumerSecret = $consumerSecret;
        $this->baseUrl = $baseUrl;

        $this->createOAuthPlugin();
        $this->createClient();
    }

    protected function createOAuthPlugin()
    {
        $this->oauthPlugin = new OauthPlugin(array(
            'consumer_key'      => $this->consumerKey,
            'consumer_secret'   => $this->consumerSecret,
            'token'             => $this->token,
            'token_secret'      => $this->tokenSecret
        ));
    }

    protected function createClient()
    {
        $this->httpClient = new Client($this->baseUrl);
        $this->httpClient->addSubscriber($this->oauthPlugin);
    }

    public function setToken($token, $tokenSecret)
    {
        $this->token = $token;
        $this->tokenSecret = $tokenSecret;

        $this->createOAuthPlugin();
        $this->createClient();
    }

    public function sendRequest($endpoint, array $parameters)
    {
        try {
            $response = $this->httpClient->post($endpoint, null, $parameters)->send();
        } catch (ClientErrorResponseException $e) {
            // 4xx
            throw new NotAuthorisedException;
        } catch (ServerErrorResponseException $e) {
            // 5xx
            throw new ServerErrorException;
        }

        try {
            $arr = $response->json();
        } catch (RuntimeException $e) {
            $arr = array();
            parse_str($response->getBody(true), $arr);
        }

        return $arr;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function getTokenSecret()
    {
        return $this->tokenSecret;
    }
}