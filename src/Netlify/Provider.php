<?php

namespace SocialiteProviders\Netlify;

use GuzzleHttp\RequestOptions;
use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use SocialiteProviders\Manager\OAuth2\User;

class Provider extends AbstractProvider
{
    public const IDENTIFIER = 'NETLIFY';

    protected function getAuthUrl($state): string
    {
        return $this->buildAuthUrlFromBase('https://app.netlify.com/authorize', $state);
    }

    protected function getTokenUrl(): string
    {
        return 'https://api.netlify.com/oauth/token';
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get('https://api.netlify.com/api/v1/user', [
            RequestOptions::HEADERS => [
                'Authorization' => "Bearer $token",
            ],
        ]);

        return json_decode((string) $response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User)->setRaw($user)->map([
            'id'       => $user['id'],
            'name'     => $user['full_name'],
            'email'    => $user['email'],
            'avatar'   => $user['avatar_url'],
        ]);
    }
}
