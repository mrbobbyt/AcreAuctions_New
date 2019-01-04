<?php
declare(strict_types = 1);

namespace App\Services\Social\Contracts;

use Google_Service_Oauth2_Userinfoplus;

interface GoogleServiceContract
{

    /**
     * Get login url to fb
     * @return string
     */
    public function getLogin();


    /**
     * Get user data
     * @return Google_Service_Oauth2_Userinfoplus
     */
    public function getProfile();

}
