<?php

namespace App\Http\Controllers\auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Adaojunior\Passport\SocialGrantException;
use Adaojunior\Passport\SocialUserResolverInterface;

use App\User;
use Socialite;

class SocialUserResolver implements SocialUserResolverInterface
{
    /**
     * Resolves user by given network and access token.
     *
     * @param string $network
     * @param string $accessToken
     * @return \Illuminate\Contracts\Auth\Authenticatable
     */
    public function resolve($network, $accessToken, $accessTokenSecret = null)
    {
        switch ($network) {
            case 'google':
                return $this->authWithGoogle($accessToken);
                break;
            case 'facebook':
            	return $this->authWithFacebook($accessToken);
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
    protected function authWithGoogle($accessToken)
    {
        $account = Socialite::driver('google')->userFromToken($accessToken);
        // dd($account);
        $user = User::where('email', $account->email)->first();
        return $user;
    }

    protected function authWithFacebook($accessToken)
    {
    	$account = Socialite::driver('facebook')->userFromToken($accessToken);
    	$user = User::where('email', $account->email)->first();
        return $user;
    }
}