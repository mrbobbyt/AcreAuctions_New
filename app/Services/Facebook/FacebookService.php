<?php

namespace App\Services\Facebook;

use Facebook\Facebook;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Authentication\AccessToken;
use Facebook\Authentication\OAuth2Client;
use Facebook\Authentication\AccessTokenMetadata;

class FacebookService
{

    /**
     * Create connect to fb
     *
     * @return Facebook
     * @throws FacebookSDKException
     */
    public function createConnect(): Facebook
    {
        return new Facebook([
                'app_id' => env('FB_APP_ID'),
                'app_secret' => env('FB_APP_SECRET'),
                'default_graph_version' => env('FB_GRAPH_VER'),
            ]);
    }


    /**
     * Get access token fb
     *
     * @param Facebook $fb
     * @return AccessToken
     * @throws FacebookSDKException
     * @throws FacebookResponseException
     */
    public function getAccessToken(Facebook $fb): AccessToken
    {
        $helper = $fb->getRedirectLoginHelper();
        if (isset($_GET['state'])) {
            $helper->getPersistentDataHandler()->set('state', $_GET['state']);
        }

        $accessToken = $helper->getAccessToken();

        if (! isset($accessToken)) {
            if ($helper->getError()) {
                return response()->json([
                    'status' => 'Error',
                    'message' =>
                        "Error: " . $helper->getError() . "\n 
                        Error Code: " . $helper->getErrorCode() . "\n 
                        Error Reason: " . $helper->getErrorReason() . "\n 
                        Error Description: " . $helper->getErrorDescription() . "\n"
                ], 401);
            } else {
                return response()->json([
                    'status' => 'Error',
                    'message' => 'Bad request',
                ], 400);
            }
        }

        return $accessToken;
    }


    /**
     * @param Facebook $fb
     * @return OAuth2Client
     */
    public function getOAuth2Client(Facebook $fb): OAuth2Client
    {
        // The OAuth 2.0 client handler helps us manage access tokens
        $oAuth2Client = $fb->getOAuth2Client();

        return $oAuth2Client;
    }


    /**
     * @param OAuth2Client $oAuth2Client
     * @param AccessToken $accessToken
     * @return AccessTokenMetadata
     */
    public function getMetaData(OAuth2Client $oAuth2Client, AccessToken $accessToken): AccessTokenMetadata
    {
        // Get the access token metadata from /debug_token
        $tokenMetadata = $oAuth2Client->debugToken($accessToken);

        return $tokenMetadata;
    }


    /**
     * Get long-live access token fb
     *
     * @param OAuth2Client $oAuth2Client
     * @param AccessToken $accessToken
     * @return AccessToken
     * @throws FacebookSDKException
     */
    public function getLongLiveAccessToken(OAuth2Client $oAuth2Client, AccessToken $accessToken): AccessToken
    {
        // Exchanges a short-lived access token for a long-lived one
        $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);

        return $accessToken;
    }

}