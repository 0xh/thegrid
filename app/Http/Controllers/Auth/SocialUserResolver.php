<?php

namespace App\Http\Controllers\auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Adaojunior\Passport\SocialGrantException;
use Adaojunior\Passport\SocialUserResolverInterface;

class SocialUserResolver implements SocialUserResolverInterface
{
    /**
     * Resolves user by given network and access token.
     *
     * @param string $network
     * @param string $accessToken
     * @return \Illuminate\Contracts\Auth\Authenticatable
     */
    public function resolve($network, $accessToken, , $accessTokenSecret = null)
    {
        switch ($network) {
            case 'google':
                return $this->authWithgoogle($accessToken);
                break;
            default:
                throw SocialGrantException::invalidNetwork();
                break;
        }
    }
    
    
    /**
     * Resolves user by google access token.
     *
     * @param string $accessToken
     * @return \App\User
     */
    protected function authWithgoogle($accessToken)
    {
        $account = Socialite::driver('google')->userFromToken($accessToken);
    }
}