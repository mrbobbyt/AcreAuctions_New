<?php

namespace App\Http\Controllers\API\v1;

use App\Services\Facebook\FacebookService;
use Facebook\Facebook;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Exceptions\FacebookResponseException;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FacebookController extends Controller
{

    protected $fbService;

    public function __construct(FacebookService $fbService)
    {
        $this->fbService = $fbService;
    }


    /**
     * Login page
     */
    public function index()
    {
        $fb = $this->fbService->createConnect();

        $helper = $fb->getRedirectLoginHelper();

        $permissions = ['email', 'public_profile']; // Optional permissions
        $loginUrl = $helper->getLoginUrl('http://localhost:8000/api/login-fb', $permissions);

        echo '<a href="' . $loginUrl . '">Log in with Facebook!</a>';
    }


    /**
     * Handle login request
     */
    public function fb()
    {
        $fb = $this->fbService->createConnect();

        $accessToken = $this->fbService->getAccessToken($fb);

        $oAuth2Client = $this->fbService->getOAuth2Client($fb);
        // vendor/facebook/graph-sdk/src/Facebook/Authentication/AccessTokenMetadata.php
        $tokenMetadata = $this->fbService->getMetaData($oAuth2Client, $accessToken);

        // Validation (these will throw FacebookSDKException's when they fail)
        $tokenMetadata->validateAppId(env('FB_APP_ID'));

        // If you know the user ID this access token belongs to, you can validate it here
        //$tokenMetadata->validateUserId('123');
        $tokenMetadata->validateExpiration();

        if (! $accessToken->isLongLived()) {
            $accessToken = $this->fbService->getLongLiveAccessToken($oAuth2Client, $accessToken);

            // Logged in
            return response()->json([
                'status' => 'Success',
                'long-lived access_token' => $accessToken->getValue(),
                //'metadata' => $tokenMetadata
            ]);
        }

        // Logged in
        return response()->json([
            'status' => 'Success',
            'access_token' => $accessToken->getValue(),
            //'metadata' => $tokenMetadata
        ]);
    }


    public function getProfile()
    {
        $fb = $this->fbService->createConnect();

        $res = $fb->get('/me', 'EAAEhn5brCoUBAHXicARmDsbHSOZCRLFjhIy1ZC4Eoca7Y2UTttUkJP4pziVR3H7lD3FMqQb8ZABRWKNsiDDC0gyfqBOiCPaTwLkWSCkZBJ0Yc08NmhkN96hc0cJgIDYQLNm5JLhTK174azXT166oTz8VBomG5uORMriQLLbHqwZDZD');

        try {
            $user = $res->getGraphNode();
        } catch (FacebookSDKException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->getMessage(),
            ], $e->getCode());
        }

        dd($user->getField( 'email' ));

    }
}
