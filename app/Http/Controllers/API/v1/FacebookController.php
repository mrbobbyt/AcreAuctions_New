<?php

namespace App\Http\Controllers\API\v1;

use App\Services\Facebook\FacebookService;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Exceptions\FacebookResponseException;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use URL;

class FacebookController extends Controller
{

    protected $fbService;
    protected $token = 'EAAEhn5brCoUBAHXicARmDsbHSOZCRLFjhIy1ZC4Eoca7Y2UTttUkJP4pziVR3H7lD3FMqQb8ZABRWKNsiDDC0gyfqBOiCPaTwLkWSCkZBJ0Yc08NmhkN96hc0cJgIDYQLNm5JLhTK174azXT166oTz8VBomG5uORMriQLLbHqwZDZD';

    public function __construct(FacebookService $fbService)
    {
        $this->fbService = $fbService;
    }


    /**
     * Login page with fb
     *
     * METHOD: get
     * URL: /api/login
     *
     * @throws FacebookSDKException
     */
    public function index()
    {
        try {
            $fb = $this->fbService->createConnect();
        } catch (FacebookSDKException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->getMessage(),
            ], $e->getCode());
        }

        $helper = $fb->getRedirectLoginHelper();

        $permissions = ['email', 'public_profile']; // Optional permissions
        $loginUrl = $helper->getLoginUrl(URL::to('/api/login-fb'), $permissions);

        return view('test-login', ['loginUrl' => $loginUrl]);
    }


    /**
     * Handle login request
     *
     * METHOD: get
     * URL: /api/login-fb
     *
     * @throws FacebookSDKException
     * @throws FacebookResponseException
     * @return JsonResponse
     */
    public function fbLogin(): JsonResponse
    {
        try {
            $fb = $this->fbService->createConnect();
        } catch (FacebookSDKException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->getMessage(),
            ], $e->getCode());
        }

        try {
            $accessToken = $this->fbService->getAccessToken($fb);
        } catch(FacebookResponseException $e) {
            // When Graph returns an error
            return response()->json([
                'status' => 'Error',
                'message' => 'Graph returned an error: ' . $e->getMessage(),
            ], $e->getCode());
        } catch(FacebookSDKException $e) {
            // When validation fails or other local issues
            return response()->json([
                'status' => 'Error',
                'message' => 'Facebook SDK returned an error: ' . $e->getMessage(),
            ], $e->getCode());
        }

        $oAuth2Client = $this->fbService->getOAuth2Client($fb);
        // vendor/facebook/graph-sdk/src/Facebook/Authentication/AccessTokenMetadata.php
        $tokenMetadata = $this->fbService->getMetaData($oAuth2Client, $accessToken);

        // Validation (these will throw FacebookSDKException's when they fail)
        $tokenMetadata->validateAppId(env('FB_APP_ID'));

        // If you know the user ID this access token belongs to, you can validate it here
        //$tokenMetadata->validateUserId('123');
        $tokenMetadata->validateExpiration();

        if (! $accessToken->isLongLived()) {
            try {
                $accessToken = $this->fbService->getLongLiveAccessToken($oAuth2Client, $accessToken);
            } catch (FacebookSDKException $e) {
                return response()->json([
                    'status' => 'Error',
                    'message' => 'Error getting long-lived access token: ' . $e->getMessage(),
                ], 400);
            }

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


    /**
     * Get test data from fb
     *
     * METHOD: get
     * URL: /api/profile-fb
     *
     * @throws FacebookSDKException
     * @return JsonResponse
     */
    public function getProfile(): JsonResponse
    {
        try {
            $fb = $this->fbService->createConnect();
        } catch (FacebookSDKException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->getMessage(),
            ], $e->getCode());
        }

        $res = $fb->get('/me', $this->token);

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


    /**
     * Logout from fb
     *
     * METHOD: get
     * URL: /api/logout-fb
     *
     * @return JsonResponse
     * @throws FacebookSDKException
     */
    public function fbLogout(): JsonResponse
    {
        try {
            $fb = $this->fbService->createConnect();
        } catch (FacebookSDKException $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->getMessage(),
            ], $e->getCode());
        }

        $helper = $fb->getRedirectLoginHelper();

        $logoutUrl = $helper->getLogoutUrl($this->token, URL::to('/api/login'));
        echo '<a href="' . $logoutUrl . '">Logout of Facebook!</a>';
    }
}
