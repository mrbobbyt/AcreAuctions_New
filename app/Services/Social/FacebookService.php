<?php
declare(strict_types = 1);

namespace App\Services\Social;

use App\Services\Social\Contracts\FacebookServiceContract;
use Facebook\Facebook;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Authentication\AccessToken;
use Facebook\Authentication\OAuth2Client;
use Facebook\Authentication\AccessTokenMetadata;
use Facebook\GraphNodes\GraphNode;

class FacebookService implements FacebookServiceContract
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
     * Get login url to fb
     *
     * @throws FacebookSDKException
     * @return string
     */
    public function getLogin(): string
    {
        $fb = $this->createConnect();

        $helper = $fb->getRedirectLoginHelper();

        $permissions = ['email']; // Optional permissions
        $loginUrl = $helper->getLoginUrl(route('home'), $permissions);

        return $loginUrl;
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


    /**
     * Handle login request
     *
     * @throws FacebookSDKException
     * @throws FacebookResponseException
     * @return array
     */
    public function fbLogin(): array
    {
        $fb = $this->createConnect();

        $accessToken = $this->getAccessToken($fb);

        $oAuth2Client = $this->getOAuth2Client($fb);
        // vendor/facebook/graph-sdk/src/Facebook/Authentication/AccessTokenMetadata.php
        $tokenMetadata = $this->getMetaData($oAuth2Client, $accessToken);

        // Validation (these will throw FacebookSDKException's when they fail)
        $tokenMetadata->validateAppId(env('FB_APP_ID'));

        // If you know the user ID this access token belongs to, you can validate it here
        //$tokenMetadata->validateUserId('123');
        $tokenMetadata->validateExpiration();

        if (! $accessToken->isLongLived()) {
            $accessToken = $this->getLongLiveAccessToken($oAuth2Client, $accessToken);

            return [
                'token' => $accessToken->getValue(),
                'fb' => $fb,
            ];
        }

        return [
            'token' => $accessToken->getValue(),
            'fb' => $fb,
        ];
    }


    /**
     * Get test data from fb
     *
     * @throws FacebookSDKException
     * @return GraphNode
     */
    public function getProfile(): GraphNode
    {
        $connection = $this->fbLogin();

        $res = $connection['fb']->get('/me?locale=en_US&fields=name,email', $connection['token']);

        $client = $res->getGraphNode();

        return $client;
    }
}