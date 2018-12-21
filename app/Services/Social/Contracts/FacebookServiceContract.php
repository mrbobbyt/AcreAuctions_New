<?php

namespace App\Services\Social\Contracts;

use Facebook\Exceptions\FacebookSDKException;
use Facebook\GraphNodes\GraphNode;

interface FacebookServiceContract
{

    /**
     * Get login url to fb
     *
     * @throws FacebookSDKException
     * @return string
     */
    public function getLogin();


    /**
     * Get user data
     *
     * @throws FacebookSDKException
     * @return GraphNode
     */
    public function getProfile();

}
