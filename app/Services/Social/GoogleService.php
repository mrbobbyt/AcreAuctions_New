<?php
declare(strict_types = 1);

namespace App\Services\Social;

use App\Services\Social\Contracts\GoogleServiceContract;
use Google_Client;
use Google_Service_Oauth2;
use Google_Service_Oauth2_Userinfoplus;

class GoogleService implements GoogleServiceContract
{
    /**
     * Create connect to google
     * @return Google_Client
     */
    protected function createConnect(): Google_Client
    {
        $google = new Google_Client();
        $google->setApplicationName(env('GOOGLE_APP_NAME'));
        $google->setClientId(env('GOOGLE_CLIENT_ID'));
        $google->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $google->setRedirectUri('http://localhost:3000/auth-callback');
        $google->setScopes(['https://www.googleapis.com/auth/userinfo.email', 'https://www.googleapis.com/auth/plus.me']);// Important!

        return $google;
    }


    /**
     * Get login url to google
     * @return string
     */
    public function getLogin(): string
    {
        $google = $this->createConnect();
        $loginUrl = $google->createAuthUrl();

        return $loginUrl;
    }


    /**
     * Get user data
     * @return Google_Service_Oauth2_Userinfoplus
     */
    public function getProfile(): Google_Service_Oauth2_Userinfoplus
    {
        $google = $this->createConnect();
        //Authenticate code from Google OAuth Flow

        $token = $google->fetchAccessTokenWithAuthCode($_GET['code']);

        //Set Access Token to make Request
        $google->setAccessToken($token['access_token']);

        //Send Client Request
        $objOAuthService = new Google_Service_Oauth2($google);

        //Get User Data from Google Plus
        $client = $objOAuthService->userinfo->get();

        return $client;
    }

}
