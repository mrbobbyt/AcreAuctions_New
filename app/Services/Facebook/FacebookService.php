<?php

namespace App\Services\Facebook;

use Facebook\Facebook;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Exceptions\FacebookResponseException;

class FacebookService
{

    public function createConnect()
    {
        try {
            $fb = new Facebook([
                'app_id' => env('FB_APP_ID'),
                'app_secret' => env('FB_APP_SECRET'),
                'default_graph_version' => env('FB_GRAPH_VER'),
            ]);
        } catch (FacebookSDKException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->getMessage(),
            ], $e->getCode());
        }

        return $fb;
    }


    public function getAccessToken(object $fb)
    {
        $helper = $fb->getRedirectLoginHelper();
        if (isset($_GET['state'])) {
            $helper->getPersistentDataHandler()->set('state', $_GET['state']);
        }

        try {
            $accessToken = $helper->getAccessToken();
        } catch(FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch(FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        if (! isset($accessToken)) {
            if ($helper->getError()) {
                header('HTTP/1.0 401 Unauthorized');
                echo "Error: " . $helper->getError() . "\n";
                echo "Error Code: " . $helper->getErrorCode() . "\n";
                echo "Error Reason: " . $helper->getErrorReason() . "\n";
                echo "Error Description: " . $helper->getErrorDescription() . "\n";
            } else {
                header('HTTP/1.0 400 Bad Request');
                echo 'Bad request';
            }
            exit;
        }

        return $accessToken;
    }


    public function getOAuth2Client(object $fb)
    {
        // The OAuth 2.0 client handler helps us manage access tokens
        $oAuth2Client = $fb->getOAuth2Client();

        return $oAuth2Client;
    }


    public function getMetaData(object $oAuth2Client, string $accessToken)
    {
        // Get the access token metadata from /debug_token
        $tokenMetadata = $oAuth2Client->debugToken($accessToken);

        return $tokenMetadata;
    }


    public function getLongLiveAccessToken(object $oAuth2Client, string $accessToken)
    {
        // Exchanges a short-lived access token for a long-lived one
        try {
            $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
        } catch (FacebookSDKException $e) {
            echo "<p>Error getting long-lived access token: " . $e->getMessage() . "</p>\n\n";
            exit;
        }

        return $accessToken;
    }

}