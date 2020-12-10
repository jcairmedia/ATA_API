<?php

namespace App\Grants;

use Laravel\Passport\Bridge\User;
use Laravel\Passport\Bridge\UserRepository;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use RuntimeException;

class FacebookUserRepository extends UserRepository
{
    /**
     * {@inheritdoc}
     */
    public function getUserEntityByUserCredentials($token, $password, $grantType, ClientEntityInterface $clientEntity)
    {
        $provider = config('auth.guards.api.provider');

        if (is_null($model = config('auth.providers.'.$provider.'.model'))) {
            throw new RuntimeException('Unable to determine authentication model from configuration.');
        }
        \Log::error(print_r($token, 1));
        $user = \App\User::findFacebookUserForPassport($token);

        if (!$user) {
            return null;
        }

        return new User($user->getAuthIdentifier());
    }
}
