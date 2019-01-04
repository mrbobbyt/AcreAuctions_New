<?php
declare(strict_types = 1);

namespace App\Services\Social;

use App\Services\Social\Contracts\FacebookServiceContract;
use Facebook\Facebook;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\GraphNodes\GraphNode;

class FacebookService implements FacebookServiceContract
{

    /**
     * Create connect to fb
     * @return Facebook
     * @throws FacebookSDKException
     */
    protected function createConnect(): Facebook
    {
        return new Facebook([
                'app_id' => env('FB_APP_ID'),
                'app_secret' => env('FB_APP_SECRET'),
                'default_graph_version' => env('FB_GRAPH_VER'),
            ]);
    }


    /**
     * Get login url to fb
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
     * Handle login request
     * @throws FacebookSDKException
     * @throws FacebookResponseException
     * @return array
     */
    protected function fbLogin(): array
    {
        $fb = $this->createConnect();

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

        // The OAuth 2.0 client handler helps us manage access tokens
        $oAuth2Client = $fb->getOAuth2Client();
        // vendor/facebook/graph-sdk/src/Facebook/Authentication/AccessTokenMetadata.php
        // Get the access token metadata from /debug_token
        $tokenMetadata = $oAuth2Client->debugToken($accessToken);

        // Validation (these will throw FacebookSDKException's when they fail)
        $tokenMetadata->validateAppId(env('FB_APP_ID'));

        // If you know the user ID this access token belongs to, you can validate it here
        //$tokenMetadata->validateUserId('123');
        $tokenMetadata->validateExpiration();

        if (! $accessToken->isLongLived()) {
            // Exchanges a short-lived access token for a long-lived one
            $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);

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